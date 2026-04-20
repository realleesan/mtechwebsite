<!-- Contact Page Content -->
<div class="contact-page" style="padding: 60px 20px; background: #f5f5f5;">
    <div class="container" style="max-width: 1200px; margin: 0 auto;">
        
        <!-- Page Title -->
        <div class="page-header" style="text-align: center; margin-bottom: 60px;">
            <h1 style="font-size: 48px; color: #1a1a1a; margin-bottom: 20px;">Contact Us</h1>
            <p style="font-size: 20px; color: #666;">Get in touch with us for any inquiries</p>
        </div>
        
        <!-- Contact Content -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
            
            <!-- Contact Form -->
            <div class="contact-form" style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h2 style="font-size: 28px; color: #1a1a1a; margin-bottom: 30px;">Send us a message</h2>
                
                <form action="?page=contact" method="POST">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Name</label>
                        <input type="text" name="your-name" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Email</label>
                        <input type="email" name="your-email" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Subject</label>
                        <input type="text" name="your-subject" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Message</label>
                        <textarea name="your-message" required rows="5" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; resize: vertical;"></textarea>
                    </div>
                    
                    <button type="submit" style="width: 100%; padding: 15px; background: #ffb600; color: #1a1a1a; border: none; border-radius: 5px; font-size: 18px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                        Send Message
                    </button>
                </form>
            </div>
            
            <!-- Contact Info -->
            <div class="contact-info" style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h2 style="font-size: 28px; color: #1a1a1a; margin-bottom: 30px;">Contact Information</h2>
                
                <div style="margin-bottom: 30px;">
                    <h3 style="font-size: 20px; color: #ffb600; margin-bottom: 10px;">Address</h3>
                    <p style="color: #666; line-height: 1.6;">123 Tech Street, Digital City<br>Technology District, 10000</p>
                </div>
                
                <div style="margin-bottom: 30px;">
                    <h3 style="font-size: 20px; color: #ffb600; margin-bottom: 10px;">Phone</h3>
                    <p style="color: #666; line-height: 1.6;">+84 123 456 789</p>
                </div>
                
                <div style="margin-bottom: 30px;">
                    <h3 style="font-size: 20px; color: #ffb600; margin-bottom: 10px;">Email</h3>
                    <p style="color: #666; line-height: 1.6;">info@mtech.com</p>
                </div>
                
                <div>
                    <h3 style="font-size: 20px; color: #ffb600; margin-bottom: 10px;">Working Hours</h3>
                    <p style="color: #666; line-height: 1.6;">Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 9:00 AM - 12:00 PM<br>Sunday: Closed</p>
                </div>
            </div>
            
        </div>
        
    </div>
</div>
