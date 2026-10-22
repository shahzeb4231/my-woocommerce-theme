<?php
defined( 'ABSPATH' ) || exit;

$countries = WC()->countries->get_countries();
$base      = WC()->countries->get_base_country();
$start     = ( isset( $_GET['action'] ) && 'register' === $_GET['action'] ) ? 'register' : 'login'; // phpcs:ignore WordPress.Security.NonceVerification

$icon_user  = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>';
$icon_shop  = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l1-5h16l1 5"/><path d="M4 9v11h16V9"/><path d="M9 20v-6h6v6"/></svg>';

/** Country <select> */
$country_select = function ( $id ) use ( $countries, $base ) {
	echo '<select name="country" id="' . esc_attr( $id ) . '" required><option value="">Choose a country</option>';
	foreach ( $countries as $code => $name ) {
		echo '<option value="' . esc_attr( $code ) . '"' . selected( $code, $base, false ) . '>' . esc_html( $name ) . '</option>';
	}
	echo '</select>';
};

/** Photo field with preview */
$photo_field = function ( $name, $label, $required, $hint ) {
	?>
	<div class="form-row form-row-wide file-field">
		<label><?php echo esc_html( $label ); ?><?php echo $required ? ' <span class="required" aria-hidden="true">*</span>' : ' <small>(optional)</small>'; ?></label>
		<label class="file-drop">
			<input type="file" name="<?php echo esc_attr( $name ); ?>" accept="image/jpeg,image/png,image/webp" <?php echo $required ? 'required' : ''; ?>>
			<span class="file-preview" aria-hidden="true"></span>
			<span class="file-text"><b>Choose a photo</b><small><?php echo esc_html( $hint ); ?></small></span>
		</label>
	</div>
	<?php
};

do_action( 'woocommerce_before_customer_login_form' );
?>
<div class="auth-wrap" data-start="<?php echo esc_attr( $start ); ?>">

	<aside class="auth-side">
		<div class="auth-side-sticky">
		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="logo-mark">X</span><span>XASSIDA <small>SN</small></span>
		</a>
		<div>
			<span class="eyebrow">Your account</span>
			<h2>One account for <span class="text-gold">everything</span></h2>
			<ul class="auth-points">
				<li><b>Shop with confidence</b><span>Save addresses, reorder in seconds and follow every order.</span></li>
				<li><b>Save your favourites</b><span>Keep a wishlist and come back to it anytime.</span></li>
				<li><b>Sell on XASSIDA SN</b><span>Open your own boutique after a quick identity check.</span></li>
			</ul>
		</div>
		<small class="auth-foot">Secure sign in &middot; Your data stays private</small>
		</div>
	</aside>

	<div class="auth-main">
		<div class="auth-tabs" role="tablist">
			<button type="button" class="auth-tab is-active" data-auth-tab="login">Sign in</button>
			<button type="button" class="auth-tab" data-auth-tab="register">Create account</button>
		</div>

		<!-- SIGN IN -->
		<div class="auth-panel is-active" data-auth-panel="login">
			<h3 class="auth-title">Welcome back</h3>
			<p class="auth-sub">Sign in to manage your orders and account.</p>

			<form class="woocommerce-form woocommerce-form-login login auth-form" method="post" novalidate>
				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="username">Username or email address&nbsp;<span class="required" aria-hidden="true">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification ?>" required aria-required="true">
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="password">Password&nbsp;<span class="required" aria-hidden="true">*</span></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true">
				</p>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<div class="auth-row">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever"> <span>Remember me</span>
					</label>
					<a class="auth-link" href="<?php echo esc_url( wp_lostpassword_url() ); ?>">Forgot password?</a>
				</div>

				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="Log in">Sign in</button>

				<?php do_action( 'woocommerce_login_form_end' ); ?>
			</form>
		</div>

		<!-- CREATE ACCOUNT -->
		<div class="auth-panel" data-auth-panel="register">
			<div class="reg-head">
				<div>
					<h3 class="auth-title">Create your account</h3>
					<p class="auth-sub" data-reg-sub>Choose how you want to use XASSIDA SN.</p>
				</div>
				<button type="button" class="type-chip" data-change-type hidden><span data-type-label></span> &middot; Change</button>
			</div>

			<div class="type-cards type-cards--inline" data-inline-choose>
				<button type="button" class="type-card" data-choose="customer">
					<span class="ti"><?php echo $icon_user; // phpcs:ignore ?></span>
					<b>Customer</b>
					<span class="d">Shop the marketplace, save favourites and track your orders.</span>
				</button>
				<button type="button" class="type-card" data-choose="vendor">
					<span class="ti"><?php echo $icon_shop; // phpcs:ignore ?></span>
					<b>Vendor</b>
					<span class="d">Open your own boutique and sell your products. Identity check required.</span>
				</button>
			</div>

			<div class="form-alert" role="alert" data-alert hidden></div>

			<!-- Customer: 2 steps -->
			<form class="auth-form reg-form" data-reg-form="customer" method="post" enctype="multipart/form-data" novalidate hidden>
				<input type="hidden" name="action" value="xsn_register">
				<input type="hidden" name="type" value="customer">
				<?php wp_nonce_field( 'xsn_register', 'xsn_nonce', false ); ?>
				<div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

				<ol class="reg-progress" data-progress><li class="is-current">Account</li><li>Profile</li></ol>

				<fieldset class="reg-step is-current" data-step="1">
					<div class="fgrid">
						<p class="form-row"><label for="c_first">First name <span class="required">*</span></label><input class="input-text" type="text" id="c_first" name="first_name" autocomplete="given-name" required></p>
						<p class="form-row"><label for="c_last">Last name <span class="required">*</span></label><input class="input-text" type="text" id="c_last" name="last_name" autocomplete="family-name" required></p>
						<p class="form-row span2"><label for="c_email">Email address <span class="required">*</span></label><input class="input-text" type="email" id="c_email" name="email" autocomplete="email" required></p>
						<p class="form-row span2"><label for="c_pass">Password <span class="required">*</span></label><input class="input-text" type="password" id="c_pass" name="password" autocomplete="new-password" minlength="8" required><small class="field-hint">At least 8 characters.</small></p>
					</div>
				</fieldset>

				<fieldset class="reg-step" data-step="2" hidden>
					<div class="fgrid">
						<p class="form-row span2"><label for="c_phone">Phone number <span class="required">*</span></label><input class="input-text" type="tel" id="c_phone" name="phone" autocomplete="tel" placeholder="+221 77 000 00 00" required></p>
						<p class="form-row span2"><label for="c_addr">Address <span class="required">*</span></label><input class="input-text" type="text" id="c_addr" name="address" autocomplete="street-address" required></p>
						<p class="form-row"><label for="c_city">City <span class="required">*</span></label><input class="input-text" type="text" id="c_city" name="city" autocomplete="address-level2" required></p>
						<p class="form-row"><label for="c_zip">Postcode</label><input class="input-text" type="text" id="c_zip" name="postcode" autocomplete="postal-code"></p>
						<p class="form-row span2"><label for="c_country">Country <span class="required">*</span></label><?php $country_select( 'c_country' ); ?></p>
						<div class="span2"><?php $photo_field( 'photo', 'Profile photo', false, 'JPG, PNG or WebP, up to 5 MB' ); ?></div>
					</div>
				</fieldset>

				<div class="reg-nav">
					<button type="button" class="btn btn-ghost" data-back hidden>Back</button>
					<button type="button" class="btn btn-primary" data-next>Continue</button>
					<button type="submit" class="btn btn-gold" data-submit hidden>Create account</button>
				</div>
			</form>

			<!-- Vendor: 3 steps -->
			<form class="auth-form reg-form" data-reg-form="vendor" method="post" enctype="multipart/form-data" novalidate hidden>
				<input type="hidden" name="action" value="xsn_register">
				<input type="hidden" name="type" value="vendor">
				<?php wp_nonce_field( 'xsn_register', 'xsn_nonce', false ); ?>
				<div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

				<ol class="reg-progress" data-progress><li class="is-current">Your details</li><li>Identity</li><li>Photos</li></ol>

				<fieldset class="reg-step is-current" data-step="1">
					<div class="fgrid">
						<p class="form-row"><label for="v_first">First name <span class="required">*</span></label><input class="input-text" type="text" id="v_first" name="first_name" autocomplete="given-name" required></p>
						<p class="form-row"><label for="v_last">Last name <span class="required">*</span></label><input class="input-text" type="text" id="v_last" name="last_name" autocomplete="family-name" required></p>
						<p class="form-row"><label for="v_email">Email address <span class="required">*</span></label><input class="input-text" type="email" id="v_email" name="email" autocomplete="email" required></p>
						<p class="form-row"><label for="v_phone">Phone number <span class="required">*</span></label><input class="input-text" type="tel" id="v_phone" name="phone" autocomplete="tel" placeholder="+221 77 000 00 00" required></p>
						<p class="form-row span2"><label for="v_addr">Address <span class="required">*</span></label><input class="input-text" type="text" id="v_addr" name="address" autocomplete="street-address" required></p>
						<p class="form-row"><label for="v_city">City <span class="required">*</span></label><input class="input-text" type="text" id="v_city" name="city" autocomplete="address-level2" required></p>
						<p class="form-row"><label for="v_zip">Postcode</label><input class="input-text" type="text" id="v_zip" name="postcode" autocomplete="postal-code"></p>
						<p class="form-row span2"><label for="v_country">Country <span class="required">*</span></label><?php $country_select( 'v_country' ); ?></p>
						<p class="form-row span2"><label for="v_shop">Shop name <span class="required">*</span></label><input class="input-text" type="text" id="v_shop" name="shop_name" minlength="3" required><small class="field-hint">This is the name customers will see on your boutique.</small></p>
						<p class="form-row span2"><label for="v_pass">Password <span class="required">*</span></label><input class="input-text" type="password" id="v_pass" name="password" autocomplete="new-password" minlength="8" required></p>
					</div>
				</fieldset>

				<fieldset class="reg-step" data-step="2" hidden>
					<p class="step-note">We verify every seller to keep the marketplace safe for customers. Your ID is stored privately and only reviewed by our team.</p>
					<div class="fgrid">
						<p class="form-row span2"><label for="v_idtype">ID type <span class="required">*</span></label>
							<select name="id_type" id="v_idtype" required>
								<option value="">Choose an ID</option>
								<option>National ID card</option>
								<option>Passport</option>
								<option>Driver's licence</option>
							</select></p>
						<p class="form-row"><label for="v_idnum">ID number <span class="required">*</span></label><input class="input-text" type="text" id="v_idnum" name="id_number" required></p>
						<p class="form-row"><label for="v_idexp">Expiry date <small>(optional)</small></label><input class="input-text" type="date" id="v_idexp" name="id_expiry"></p>
					</div>
				</fieldset>

				<fieldset class="reg-step" data-step="3" hidden>
					<?php $photo_field( 'photo', 'Your profile photo', true, 'A clear photo of your face' ); ?>
					<?php $photo_field( 'id_front', 'ID card photo (front)', true, 'All four corners and the text must be readable' ); ?>
					<?php $photo_field( 'id_back', 'ID card photo (back)', false, 'If your ID has a back side' ); ?>
					<label class="terms-check"><input type="checkbox" name="terms" value="1" required> <span>I confirm the details are true and I accept the <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>" target="_blank" rel="noopener">seller terms</a>.</span></label>
				</fieldset>

				<div class="reg-nav">
					<button type="button" class="btn btn-ghost" data-back hidden>Back</button>
					<button type="button" class="btn btn-primary" data-next>Continue</button>
					<button type="submit" class="btn btn-gold" data-submit hidden>Submit application</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Account type pop-up -->
<div class="xmodal" id="reg-modal" hidden role="dialog" aria-modal="true" aria-labelledby="reg-modal-title">
	<button type="button" class="xmodal-backdrop" data-modal-close aria-label="Close"></button>
	<div class="xmodal-box">
		<button type="button" class="xmodal-x" data-modal-close aria-label="Close">&times;</button>
		<h3 id="reg-modal-title">How would you like to join?</h3>
		<p>Choose the account that fits you best.</p>
		<div class="type-cards">
			<button type="button" class="type-card" data-choose="customer">
				<span class="ti"><?php echo $icon_user; // phpcs:ignore ?></span>
				<b>Customer</b>
				<span class="d">Shop the marketplace, save favourites and track your orders.</span>
			</button>
			<button type="button" class="type-card" data-choose="vendor">
				<span class="ti"><?php echo $icon_shop; // phpcs:ignore ?></span>
				<b>Vendor</b>
				<span class="d">Open your own boutique and sell your products. Identity check required.</span>
			</button>
		</div>
	</div>
</div>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
