<?php
echo'
<style>
    /* Styling for the floating email button */
    .floating-email-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #007bff;
        color: #fff;
        padding: 10px;
        border-radius: 50px;
        text-align: center;
        text-decoration: none;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease;
        z-index: 9999; /* Ensure the button is on top of other elements */
    }

    /* Hover effect */
    .floating-email-button:hover {
        background-color: #0056b3;
        color: #fff;
        cursor: pointer;
    }
</style>

<a href="mailto:'.SMTP_EMAIL.'" class="floating-email-button">
    📧 Email Us
</a>';