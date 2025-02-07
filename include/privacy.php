<?php
require_once 'head.php';
require_once 'top_nav.php';
require_once 'breadcrumb.php';
echo '
<div class="page-banner">
  <div class="container">
    <div class="row">
      <div class="col-md-12 col-12">
        <h1 class="mb-0">' . moduleName(CONTROLER) . '</h1>
      </div>
    </div>
  </div>
</div>
<div class="page-content">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="terms-content">
          <div class="terms-text">
            <h4 class="text-center">'.SITE_NAME.' is committed to protecting the privacy of our users. This Privacy Policy outlines how we collect, use, disclose, and safeguard your personal information when you visit our website <a href="'.SITE_URL.'" class="text-primary">'.SITE_NAME.'</a> and use our services.</h4>
            <hr>
            <h5>Information We Collect</h5>
            <p>We may collect personal information directly from you when you register on our website, enroll in courses, participate in forums or discussions, complete surveys, or otherwise interact with our services. The types of personal information we may collect include:</p>
            <ul>
              <li>Contact Information (such as name, email address, mailing address, and phone number)</li>
              <li>Account Credentials (such as username and password)</li>
              <li>Payment Information (such as credit card details)</li>
              <li>Academic and Professional Information (such as educational background, qualifications, and employment history)
              </li>
              <li>Communication Preferences</li>
            </ul>
            <p>We may also automatically collect certain information about your device and usage of the Site, such as your IP address, browser type, operating system, referring URLs, and pages viewed. This information helps us analyze trends, administer the Site, track users movements around the Site, and gather demographic information about our user base.</p>

            <h5>How We Use Your Information</h5>
            <p>We use the information we collect for various purposes, including to:</p>
            <ul>
              <li>Provide and personalize our services</li>
              <li>Process enrollments and transactions</li>
              <li>Communicate with you about your account and courses</li>
              <li>Respond to your inquiries and provide customer support</li>
              <li>Analyze and improve the quality of our services</li>
              <li>Send you promotional and marketing communications</li>
              <li>Comply with legal obligations</li>
            </ul>

            <h5>Information Sharing</h5>
            <p>We may share your personal information with third-party service providers who assist us in providing and managing our services, such as payment processors, hosting providers, and marketing platforms. These service providers are obligated to protect your information and are prohibited from using it for any other purpose.</p>
            <p>We may also disclose your information in response to legal requests, such as court orders or subpoenas, or to protect our rights, property, or safety, or the rights, property, or safety of others.</p>
            
            <h5>Data Security</h5>
            <p>We implement reasonable security measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the internet or electronic storage is completely secure, so we cannot guarantee absolute security.</p>

            <h5>Your Choices</h5>
            <p>You may update or correct your account information at any time by logging into your account settings. You may also unsubscribe from our marketing communications by following the instructions provided in the messages.</p>
            
            <h5>Children\'s Privacy</h5>
            <p>Our services are not directed to individuals under the age of 13, and we do not knowingly collect personal information from children under 13. If you become aware that a child under 13 has provided us with personal information, please contact us immediately.</p>
            
            <h5>Changes to This Privacy Policy</h5>
            <p>We may update this Privacy Policy from time to time to reflect changes in our practices or applicable laws. We will notify you of any material changes by posting the updated Privacy Policy on the Site. Your continued use of the Site after the effective date of the revised Privacy Policy constitutes your acceptance of the changes.</p>
            
            <h5>Contact Us</h5>
            <p>If you have any questions or concerns about this Privacy Policy or our privacy practices, please contact us at <a href="mailto:info@mul.edu.pk">info@mul.edu.pk</a>.</p>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>';
require_once 'footer.php';
?>