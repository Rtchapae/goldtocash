<template>
	<section class="section-contact">
		<div class="container">
			<h1>Contact Us</h1>
			<div class="row">
				<div class="col-12 col-lg-8">
					<h3>Getting in&nbsp;touch with Gold to&nbsp;Cash is&nbsp;easy!</h3>
					<p>We&nbsp;are always happy to&nbsp;assist and we&nbsp;do&nbsp;it&nbsp;fast. Please complete the
						form to&nbsp;send&nbsp;us a&nbsp;message.</p>
					<form action="/sendmail" method="post">
						<div class="control-group form-group">
							<input type="text" class="form-control" id="name" name="name" required=""
								data-validation-required-message="Please enter your name.">
							<label for="name">Full Name:</label>
							<p class="help-block"></p>
						</div>
						<div class="row">
							<div class="col-12 col-md-6">
								<div class="control-group form-group">
									<input type="tel" class="form-control" id="phone" name="phone" required=""
										data-validation-required-message="Please enter your phone number.">
									<label for="phone">Phone Number:</label>
								</div>
							</div>
							<div class="col-12 col-md-6">
								<div class="control-group form-group">
									<input type="email" class="form-control" id="email" name="email" required=""
										data-validation-required-message="Please enter your email address.">
									<label for="email">Email Address:</label>
								</div>
							</div>
						</div>
						<div class="control-group form-group">
							<textarea rows="5" cols="100" class="form-control textarea-no-resize" id="message" name="message" required=""
								data-validation-required-message="Please enter your message" maxlength="999"></textarea>
							<label for="message">Message:</label>
						</div>
						<div id="success"></div>
						<button type="submit" class="btn btn-primary">Send Message</button>
					</form>
				</div>
				<div class="col-12">
					<p>Feel free to&nbsp;send&nbsp;us an&nbsp;email at&nbsp;
						<a href="mailto:hello@goldtocash.us" target="_blank">hello@goldtocash.us</a>
					</p>
					<p>Or&nbsp;call our toll-free number <a href="tel:5642377332" target="_blank">(564) 237-7332</a>
						(Mon - Fri, 9am - 5pm PST)</p>
				</div>
			</div>
		</div>
	</section>
</template>

<style scoped>
.textarea-no-resize {
	resize: none;
}

.warning-error,
.warning-success {
	margin-bottom: 2.617924em;
	background-color: red;
	margin-left: 0;
	border-radius: 2px;
	color: #fff;
	clear: both;
	border-left: 0.6180469716em solid rgba(0, 0, 0, .15);
	padding: 1em 2em 1em 3.5em;
	position: relative;
	list-style: none outside;
}

.warning-success {
	background-color: green;
}
</style>
