<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="main-container profile">
	<header class="header">


		
		<nav>
			<ul>
				<li>
					<a href="<?php echo home_url('profile-page'); ?> ">
						<svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M17 19C17 17.6044 17 16.9067 16.8278 16.3389C16.44 15.0605 15.4395 14.06 14.1611 13.6722C13.5933 13.5 12.8956 13.5 11.5 13.5H6.5C5.10444 13.5 4.40665 13.5 3.83886 13.6722C2.56045 14.06 1.56004 15.0605 1.17224 16.3389C1 16.9067 1 17.6044 1 19M13.5 5.5C13.5 7.98528 11.4853 10 9 10C6.51472 10 4.5 7.98528 4.5 5.5C4.5 3.01472 6.51472 1 9 1C11.4853 1 13.5 3.01472 13.5 5.5Z" stroke="#111111" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				</li>
				<li>
					<a href="<?php echo home_url('favorites'); ?> ">

						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M11.5257 5.03591C9.61989 2.80783 6.44179 2.20848 4.05391 4.24874C1.66603 6.28899 1.32985 9.7002 3.20507 12.1132C4.76418 14.1195 9.4826 18.3508 11.029 19.7204C11.2021 19.8736 11.2886 19.9502 11.3895 19.9803C11.4775 20.0066 11.5739 20.0066 11.662 19.9803C11.7629 19.9502 11.8494 19.8736 12.0224 19.7204C13.5688 18.3508 18.2873 14.1195 19.8464 12.1132C21.7216 9.7002 21.4265 6.26753 18.9975 4.24874C16.5686 2.22994 13.4316 2.80783 11.5257 5.03591Z" stroke="#111111" stroke-width="2" stroke-linejoin="round"/>
						</svg>

					</a>
				</li>
				<li>
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M9.35395 21C10.0591 21.6224 10.9853 22 11.9998 22C13.0142 22 13.9405 21.6224 14.6456 21M17.9998 8C17.9998 6.4087 17.3676 4.88258 16.2424 3.75736C15.1172 2.63214 13.5911 2 11.9998 2C10.4085 2 8.88235 2.63214 7.75713 3.75736C6.63192 4.88258 5.99977 6.4087 5.99977 8C5.99977 11.0902 5.22024 13.206 4.34944 14.6054C3.6149 15.7859 3.24763 16.3761 3.2611 16.5408C3.27601 16.7231 3.31463 16.7926 3.46155 16.9016C3.59423 17 4.19237 17 5.38863 17H18.6109C19.8072 17 20.4053 17 20.538 16.9016C20.6849 16.7926 20.7235 16.7231 20.7384 16.5408C20.7519 16.3761 20.3846 15.7859 19.6501 14.6054C18.7793 13.206 17.9998 11.0902 17.9998 8Z" stroke="#111111" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<circle cx="18" cy="9" r="4" fill="#E22400"/>
					</svg>
				</li>
			</ul>
		</nav>

		
		<?php $current_uri = $_SERVER['REQUEST_URI']; ?>
			<?php if ( strpos($current_uri, '/dashboard/reports-data') === 0 ): ?>
				<?php if ( dokan_is_user_seller( get_current_user_id() ) ): ?>

					<div class="seller-earnings">
							קרדיט 
							<span class="seller-earnings__accent">
							<?php 
								$balance = dokan_get_seller_balance( get_current_user_id(), false );
								echo wc_price( $balance );
							?>
												<svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M8.66683 2.33333C8.66683 3.06971 7.02521 3.66667 5.00016 3.66667C2.97512 3.66667 1.3335 3.06971 1.3335 2.33333M8.66683 2.33333C8.66683 1.59695 7.02521 1 5.00016 1C2.97512 1 1.3335 1.59695 1.3335 2.33333M8.66683 2.33333V3.33333M1.3335 2.33333V10.3333C1.3335 11.0697 2.97512 11.6667 5.00016 11.6667M5.00016 6.33333C4.8878 6.33333 4.77662 6.3315 4.66683 6.3279C2.798 6.26666 1.3335 5.69552 1.3335 5M5.00016 9C2.97512 9 1.3335 8.40305 1.3335 7.66667M14.6668 6.66667C14.6668 7.40305 13.0252 8 11.0002 8C8.97512 8 7.3335 7.40305 7.3335 6.66667M14.6668 6.66667C14.6668 5.93029 13.0252 5.33333 11.0002 5.33333C8.97512 5.33333 7.3335 5.93029 7.3335 6.66667M14.6668 6.66667V11.6667C14.6668 12.403 13.0252 13 11.0002 13C8.97512 13 7.3335 12.403 7.3335 11.6667V6.66667M14.6668 9.16667C14.6668 9.90305 13.0252 10.5 11.0002 10.5C8.97512 10.5 7.3335 9.90305 7.3335 9.16667" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
				</span>
					</div>
				<?php endif; ?>
			<?php endif; ?>




	</header>