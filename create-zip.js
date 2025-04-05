const fs = require('fs');
const archiver = require('archiver');
const path = require('path');

// Define the path to the theme folder and the path for the zip file
const outputPath = path.resolve(__dirname, 'export-theme/omnisBase.zip');
const themeFolderPath = path.resolve(__dirname);

// Check if the directory exists, and create it if it doesn't
const outputDir = path.dirname(outputPath);
if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true }); // Create directory recursively
}

// Create a stream for writing the zip file
const output = fs.createWriteStream(outputPath);
const archive = archiver('zip', {
    zlib: { level: 9 } // Compression level
});

// Handle errors during the archive process
output.on('close', () => {
    console.log(`Archive created: ${archive.pointer()} bytes`);
});

archive.on('error', (err) => {
    console.error('Archiving error:', err);
    throw err;
});

// Log the progress of files being processed
archive.on('progress', (progress) => {
    console.log(`Files processed: ${progress.entries.processed}, total expected: ${progress.entries.total}`);
});

// Log each file before it's added to the archive
archive.on('entry', (entry) => {
    console.log(`Archiving file: ${entry.name}`);
});

// Connect the archive to the output stream
archive.pipe(output);

// Add all files and folders from the theme folder, excluding node_modules, vendor, .git, and export-theme
archive.glob('**/*', {
    cwd: themeFolderPath,
    ignore: ['node_modules/**', 'vendor/**', '.git/**', 'export-theme/**'] // Exclude export-theme folder
});

// Finalize the archiving process
archive.finalize().then(() => {
    console.log('Archiving process completed.');
});