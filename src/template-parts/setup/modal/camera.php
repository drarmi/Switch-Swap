<div class="camera-wrap-editor" style="display: none;">
    <div class="close-stream">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 6L6 18M6 6L18 18" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
    <div>
        <video id="video-editor" width="640" height="480" autoplay></video>
        <button id="captureBtn-editor"></button>
        <button id="saveBtn-editor" style="display: none;"><?php esc_html_e("לְהַצִיל", "swap"); ?></button>
        <canvas id="canvas-editor"class="canvas-editor" style="display: none;"></canvas>
        <img id="snapshot-editor" src="" alt="photo" style="display: none;">
    </div>
</div>