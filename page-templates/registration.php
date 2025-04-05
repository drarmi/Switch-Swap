<?php
/* Template Name: User Registration */
get_header();

$home_url         = untrailingslashit( home_url() );
$custom_store_url = dokan_get_option( 'custom_store_url', 'dokan_general', 'store' );
?>

<div class="registration-container">
    <!-- Progress Bar -->
    <div class="progress-bar">
        <div class="progress" id="progress"></div>
    </div>

    <!-- Step 1: Welcome Screen -->
    <div class="step step-1 active" id="step-1">
        <h2>Welcome!</h2>
        <p>Please go through the registration process to start using the application.</p>
        <button class="btn-next" data-step="1">Start</button>
    </div>

    <!-- Step 2: Personal Information -->
    <div class="step step-2" id="step-2">
        <h2>Personal Information (only required at registration)</h2>
        <form id="registration-form">
            <?php wp_nonce_field('registration_nonce', 'security'); ?>
            <div class="field"></div>

            <div class="block">
                <label for="name">Name <span>*</span></label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="block">
                <label for="password">Password <span>*</span></label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="block">
                <label for="email">Email <span>*</span></label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="block">
                <label for="dob">Date of Birth (18+ required) <span>*</span></label>
                <input type="date" id="dob" name="dob" min="1900-01-01" required>
            </div>
            
            <div class="block">
                <label>Gender (optional)</label>
                <label><input type="radio" name="gender" value="male"> Male</label>
                <label><input type="radio" name="gender" value="female"> Female</label>
                <label><input type="radio" name="gender" value="other"> Other</label>
            </div>
            
            <div class="block">
                <label for="role">Role </label>
                <select id="role" name="role" required>
                    <option value="customer">Customer</option>
                    <option value="seller">Vendor</option>
                </select> 
            </div>
            
            <!-- Shop Name and URL fields for sellers --> 
            <div id="vendor-fields" style="display:none;">
                <div class="form-row form-group form-row-wide">
                    <label for="shop-name">Shop Name <span class="required">*</span></label>
                    <input type="text" class="input-text form-control" name="shopname" id="shop-name" placeholder="Enter your shop name">
                </div>

                <div class="form-row form-group form-row-wide">
                    <label for="shop-url">Shop URL <span class="required">*</span></label>
                    <strong id="url-alert-msg" class="pull-right text-success"></strong>
                    <input type="text" class="input-text form-control" name="shopurl" id="shop-url" placeholder="Unique URL for your store">
                    <small><?php echo esc_url( $home_url . '/' . $custom_store_url ) . '/'; ?><strong id="url-alert" class="text-success"></strong></small>
                </div>
            </div>

            <div class="block">
                <label for="profile_photo">Profile Photo (optional)</label>
                <input type="file" id="profile_photo" name="profile_photo"> 
            </div>

            <div class="block">
                <label>
                    <input type="checkbox" id="terms" name="terms" required>
                    I agree to the <a href="/terms">Terms and Conditions</a>
                </label>
            </div>
            
            <div class="buttons">
                <button type="button" class="btn-prev" data-step="2">Back</button>
                <button type="button" class="btn-next" data-step="2">Continue</button>
            </div> 
        </form>
    </div>

    <!-- Step 3: Personalization -->
    <div class="step step-3" id="step-3">
        <h2>Personalization</h2>
        <p>What would you like to do in the app?</p>
        <div class="options">
            <label><input type="radio" name="app_usage" value="rent"> Rent</label>
            <label><input type="radio" name="app_usage" value="buy"> Buy</label>
            <label><input type="radio" name="app_usage" value="lease"> Lease</label>
            <label><input type="radio" name="app_usage" value="sell"> Sell</label>
        </div>
        <div class="buttons">
            <button type="button" class="btn-prev" data-step="3">Back</button>
            <button type="button" class="btn-next" data-step="3">Continue</button>
            <button type="button" class="btn-skip" data-step="3">Skip</button>
        </div> 
    </div>

    <!-- Step 4: Style Preferences -->
    <div class="step step-4" id="step-4">
        <h2>What is your everyday style? Choose the style that suits you</h2>
        <div class="options">
            <label><input type="checkbox" name="styles[]" value="wedding"> Wedding</label>
            <label><input type="checkbox" name="styles[]" value="evening_event"> Evening Event</label>
            <label><input type="checkbox" name="styles[]" value="date_dinner"> Date or Dinner at a Restaurant</label>
            <label><input type="checkbox" name="styles[]" value="festivals"> Festivals</label>
            <label><input type="checkbox" name="styles[]" value="party_birthday"> Party / Birthday</label>
            <label><input type="checkbox" name="styles[]" value="vacation"> Vacation</label>
            <label><input type="checkbox" name="styles[]" value="daily"> Daily</label>
            <label><input type="checkbox" name="styles[]" value="work"> Work</label>
        </div>
        <div class="buttons">
            <button type="button" class="btn-prev" data-step="4">Back</button>
            <button type="button" class="btn-next" data-step="4">Continue</button>
            <button type="button" class="btn-skip" data-step="4">Skip</button>
        </div>
    </div>

    <!-- Step 5: Registration Completion -->
    <div class="step step-5" id="step-5">
        <h2>Registration Completed!</h2>
        <p>Your profile has been successfully created.</p>
        <div class="profile-summary">
            <img id="profile-avatar" src="default_avatar.png" alt="Profile Avatar">
            <p><strong>Name:</strong> <span id="profile-name"></span></p>
            <p><strong>Email:</strong> <span id="profile-email"></span></p>
        </div>
        <div class="action-buttons">
            <button onclick="window.location.href='/search'">Start Searching for Items</button>
            <button onclick="window.location.href='/upload-product'">Upload Products</button>
        </div>
        <button onclick="window.location.href='/home'">Go to Home</button>
    </div>
</div>

<?php get_footer(); ?>