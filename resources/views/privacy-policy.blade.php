@extends('layouts.guest')

@section('title', 'Privacy Policy')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h1 class="text-center">Privacy Policy</h1>
                    <p class="text-center text-muted">Last updated: {{ date('F d, Y') }}</p>
                </div>
                <div class="card-body">
                    <div class="privacy-content">
                        <h2>1. Information We Collect</h2>
                        <p>We collect information you provide directly to us, such as when you create an account, use
                            our services, or contact us for support. This may include:</p>
                        <ul>
                            <li>Personal information (name, email address, phone number)</li>
                            <li>Business information (company name, address, business details)</li>
                            <li>Usage data (how you interact with our services)</li>
                            <li>Device information (IP address, browser type, operating system)</li>
                        </ul>

                        <h2>2. How We Use Your Information</h2>
                        <p>We use the information we collect to:</p>
                        <ul>
                            <li>Provide, maintain, and improve our services</li>
                            <li>Process transactions and send related information</li>
                            <li>Send technical notices, updates, and support messages</li>
                            <li>Respond to your comments and questions</li>
                            <li>Monitor and analyze trends and usage</li>
                            <li>Detect, investigate, and prevent security incidents</li>
                        </ul>

                        <h2>3. Information Sharing and Disclosure</h2>
                        <p>We do not sell, trade, or otherwise transfer your personal information to third parties
                            without your consent, except in the following circumstances:</p>
                        <ul>
                            <li>With your explicit consent</li>
                            <li>To comply with legal obligations</li>
                            <li>To protect our rights and prevent fraud</li>
                            <li>With service providers who assist us in operating our services</li>
                        </ul>

                        <h2>4. Data Security</h2>
                        <p>We implement appropriate technical and organizational measures to protect your personal
                            information against unauthorized access, alteration, disclosure, or destruction. However, no
                            method of transmission over the internet or electronic storage is 100% secure.</p>

                        <h2>5. Data Retention</h2>
                        <p>We retain your personal information for as long as necessary to provide our services and
                            fulfill the purposes outlined in this privacy policy, unless a longer retention period is
                            required or permitted by law.</p>

                        <h2>6. Your Rights</h2>
                        <p>Depending on your location, you may have certain rights regarding your personal information,
                            including:</p>
                        <ul>
                            <li>The right to access your personal information</li>
                            <li>The right to correct inaccurate information</li>
                            <li>The right to delete your personal information</li>
                            <li>The right to restrict or object to processing</li>
                            <li>The right to data portability</li>
                        </ul>

                        <h2>7. Cookies and Tracking Technologies</h2>
                        <p>We use cookies and similar tracking technologies to collect and use personal information
                            about you. You can control cookies through your browser settings, but disabling cookies may
                            affect the functionality of our services.</p>

                        <h2>8. Third-Party Services</h2>
                        <p>Our services may contain links to third-party websites or services. We are not responsible
                            for the privacy practices of these third parties. We encourage you to read their privacy
                            policies.</p>

                        <h2>9. Children's Privacy</h2>
                        <p>Our services are not intended for children under 13 years of age. We do not knowingly collect
                            personal information from children under 13.</p>

                        <h2>10. Changes to This Privacy Policy</h2>
                        <p>We may update this privacy policy from time to time. We will notify you of any changes by
                            posting the new privacy policy on this page and updating the "Last updated" date.</p>

                        <h2>11. Contact Us</h2>
                        <p>If you have any questions about this privacy policy or our privacy practices, please contact
                            us at:</p>
                        <ul>
                            <li>Email: privacy@akalptechnomediasolutions.com</li>
                            <li>Phone: [Your Phone Number]</li>
                            <li>Address: [Your Business Address]</li>
                        </ul>

                        <div class="text-center mt-4">
                            <a href="{{ url('/') }}" class="btn btn-primary">Return to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.privacy-content {
    line-height: 1.6;
}

.privacy-content h2 {
    color: #333;
    margin-top: 2rem;
    margin-bottom: 1rem;
    border-bottom: 2px solid #007bff;
    padding-bottom: 0.5rem;
}

.privacy-content h1 {
    color: #007bff;
    margin-bottom: 1rem;
}

.privacy-content ul {
    margin-bottom: 1rem;
}

.privacy-content li {
    margin-bottom: 0.5rem;
}

.card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.container {
    margin-top: 2rem;
    margin-bottom: 2rem;
}
</style>
@endsection