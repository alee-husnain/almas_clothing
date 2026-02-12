<?php include('../templates/header.php'); ?>

<main class="contact-page">
    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="hero-content">
            <h1>Get in Touch</h1>
            <p>We're here to help and answer any questions you might have.</p>
        </div>
    </section>

    <div class="contact-container">
        <!-- Quick Contact Cards -->
        <section class="quick-contact">
            <div class="contact-card">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3>Email Us</h3>
                <p>Get in touch via email</p>
                <a href="mailto:support@almasclothing.com">support@almasclothing.com</a>
            </div>

            <div class="contact-card">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </div>
                <h3>Call Us</h3>
                <p>Mon - Sat, 9:00 AM - 6:00 PM</p>
                <a href="tel:+923000000000">+92 300 0000000</a>
            </div>

            <div class="contact-card">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3>Visit Us</h3>
                <p>Our Store Location</p>
                <address>123 Fashion Street, Lahore, Pakistan</address>
            </div>
        </section>

        <!-- Main Contact Section -->
        <section class="contact-main">
            <div class="contact-grid">
                <!-- Contact Form -->
                <div class="contact-form-wrap">
                    <h2>Send us a Message</h2>
                    <p class="form-intro">Fill out the form below and we'll get back to you as soon as possible.</p>
                    
                    <div id="form-message" class="form-message" role="status" aria-live="polite" style="display: none;"></div>
                     <form id="contact-form" style="width:100%;">
                        <!-- Row 1 -->
                        <div style="display:flex; gap:20px; margin-bottom:20px;">
                        <div style="flex:1; display:flex; flex-direction:column;">
                            <label for="name" style="margin-bottom:6px; font-size:14px; font-weight:600;">
                            Your Name <span style="color:red;">*</span>
                            </label>
                            <input type="text" id="name" name="name" required minlength="2"
                            style="padding:12px; border:1px solid #ccc; border-radius:6px; font-size:14px;">
                            <span id="name-error" style="font-size:12px; color:red; margin-top:4px;"></span>
                        </div>

                        <div style="flex:1; display:flex; flex-direction:column;">
                            <label for="email" style="margin-bottom:6px; font-size:14px; font-weight:600;">
                            Your Email <span style="color:red;">*</span>
                            </label>
                            <input type="email" id="email" name="email" required
                            style="padding:12px; border:1px solid #ccc; border-radius:6px; font-size:14px;">
                            <span id="email-error" style="font-size:12px; color:red; margin-top:4px;"></span>
                        </div>
                        </div>

                        <!-- Row 2 -->
                        <div style="display:flex; gap:20px; margin-bottom:20px;">
                        <div style="flex:1; display:flex; flex-direction:column;">
                            <label for="phone" style="margin-bottom:6px; font-size:14px; font-weight:600;">
                            Phone Number
                            </label>
                            <input type="tel" id="phone" name="phone" placeholder="+92300xxxxxxx"
                            style="padding:12px; border:1px solid #ccc; border-radius:6px; font-size:14px;">
                            <span id="phone-error" style="font-size:12px; color:red; margin-top:4px;"></span>
                        </div>

                        <div style="flex:1; display:flex; flex-direction:column;">
                            <label for="subject" style="margin-bottom:6px; font-size:14px; font-weight:600;">
                            Subject
                            </label>
                            <input type="text" id="subject" name="subject"
                            style="padding:12px; border:1px solid #ccc; border-radius:6px; font-size:14px;">
                            <span id="subject-error" style="font-size:12px; color:red; margin-top:4px;"></span>
                        </div>
                        </div>

                        <!-- Message -->
                        <div style="display:flex; flex-direction:column; margin-bottom:30px;">
                        <label for="message" style="margin-bottom:6px; font-size:14px; font-weight:600;">
                            Your Message <span style="color:red;">*</span>
                        </label>
                        <textarea id="message" name="message" rows="6" required minlength="10"
                            style="padding:12px; border:1px solid #ccc; border-radius:6px; font-size:14px; resize:vertical;"></textarea>
                        <span id="message-error" style="font-size:12px; color:red; margin-top:4px;"></span>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                        style="display:inline-flex; align-items:center; justify-content:center;
                                padding:12px 30px; font-size:15px; font-weight:600;
                                background:#111; color:#fff; border:none; border-radius:6px;
                                cursor:pointer; min-width:180px;">
                        <span class="btn-text">Send Message</span>

                        <span class="btn-loading" style="display:none; margin-left:8px;">
                            <svg viewBox="0 0 50 50" width="20" height="20">
                            <circle cx="25" cy="25" r="20"
                                    fill="none" stroke="#fff" stroke-width="4"></circle>
                            </svg>
                        </span>
                        </button>
                    </form>
                </div>

                <!-- Map and Info -->
                <div class="contact-info">
                    <div class="info-card">
                        <h3>Store Hours</h3>
                        <ul class="hours-list">
                            <li>
                                <span>Monday - Friday</span>
                                <span>9:00 AM - 6:00 PM</span>
                            </li>
                            <li>
                                <span>Saturday</span>
                                <span>10:00 AM - 4:00 PM</span>
                            </li>
                            <li>
                                <span>Sunday</span>
                                <span>Closed</span>
                            </li>
                        </ul>
                    </div>

                    <div class="map-container">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3401.5353455240407!2d74.3023514!3d31.5036412!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzHCsDMwJzEzLjEiTiA3NMKwMTgnMDguNSJF!5e0!3m2!1sen!2s!4v1635555555555!5m2!1sen!2s" 
                            width="100%" 
                            height="300" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQs Section -->
        <section class="contact-faqs">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h3>What are your shipping times?</h3>
                    <p>We typically process and ship orders within 1-2 business days. Standard shipping takes 3-5 business days within Pakistan.</p>
                </div>
                <div class="faq-item">
                    <h3>What is your return policy?</h3>
                    <p>We offer a 30-day return policy for unused items in original packaging. See our returns page for more details.</p>
                </div>
                <div class="faq-item">
                    <h3>Do you ship internationally?</h3>
                    <p>Yes, we ship to select international locations. International shipping times vary by destination.</p>
                </div>
                <div class="faq-item">
                    <h3>How can I track my order?</h3>
                    <p>Once your order ships, you'll receive a tracking number via email to monitor your delivery status.</p>
                </div>
            </div>
        </section>
    </div>
</main>

<script>
// Enhanced contact form handling with client validation, ARIA updates and auto-hide
(() => {
    const form = document.getElementById('contact-form');
    const formMessage = document.getElementById('form-message');
    const submitBtn = form.querySelector('button[type="submit"]');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    let hideTimer = null;

    function setLoading(isLoading) {
        if (isLoading) {
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline-block';
            submitBtn.setAttribute('aria-busy', 'true');
            submitBtn.disabled = true;
        } else {
            btnText.style.display = 'inline-block';
            btnLoading.style.display = 'none';
            submitBtn.removeAttribute('aria-busy');
            submitBtn.disabled = false;
        }
    }

    function showMessage(type, text) {
        formMessage.className = 'form-message ' + (type === 'success' ? 'success' : 'error');
        formMessage.textContent = text;
        formMessage.style.display = 'block';
        formMessage.focus && formMessage.focus();

        // Auto-hide success messages after 6s
        if (type === 'success') {
            if (hideTimer) clearTimeout(hideTimer);
            hideTimer = setTimeout(() => {
                formMessage.style.display = 'none';
            }, 6000);
        }
    }

    function clearFieldErrors() {
        form.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }

    function validateForm() {
        clearFieldErrors();
        const errors = {};
        const name = form.name.value.trim();
        const email = form.email.value.trim();
        const message = form.message.value.trim();
        const phone = form.phone.value.trim();

        if (name.length < 2) errors.name = 'Please enter your name (at least 2 characters).';
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.email = 'Please enter a valid email address.';
        if (message.length < 10) errors.message = 'Message must be at least 10 characters.';
        if (phone && !/^\+?\d{7,15}$/.test(phone)) errors.phone = 'Please enter a valid phone number.';

        // Map errors to UI
        Object.entries(errors).forEach(([field, msg]) => {
            const el = document.getElementById(`${field}-error`);
            if (el) el.textContent = msg;
        });

        return Object.keys(errors).length === 0;
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // client-side validation
        if (!validateForm()) {
            showMessage('error', 'Please fix the errors in the form and try again.');
            return;
        }

        setLoading(true);
        formMessage.style.display = 'none';

        try {
            const formData = new FormData(form);
            const res = await fetch('send_message.php', {
                method: 'POST',
                body: new URLSearchParams(formData)
            });
            const data = await res.json();

            if (data && data.success) {
                showMessage('success', data.message || 'Message sent. We will reply shortly.');
                form.reset();
                // move focus to the success region for screen readers
                formMessage.setAttribute('tabindex', '-1');
                formMessage.focus();
            } else {
                showMessage('error', data.message || 'Unable to send message.');
                if (data && data.errors) {
                    Object.entries(data.errors).forEach(([field, msg]) => {
                        const el = document.getElementById(`${field}-error`);
                        if (el) el.textContent = msg;
                    });
                }
            }
        } catch (err) {
            showMessage('error', 'An error occurred. Please try again later.');
        } finally {
            setLoading(false);
            // ensure message is visible
            formMessage.style.display = 'block';
            if (formMessage.getBoundingClientRect().top < 0) {
                formMessage.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });

    // Clear field error on input
    form.querySelectorAll('input, textarea').forEach(inp => {
        inp.addEventListener('input', () => {
            const err = document.getElementById(inp.id + '-error');
            if (err) err.textContent = '';
            formMessage.style.display = 'none';
        });
    });
})();
</script>

<?php include('../templates/footer.php'); ?>
