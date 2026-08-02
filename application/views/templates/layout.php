<?php
$vars = $this->load->get_vars();

// Header
$this->load->view('templates/header', $vars);

// Top Navbar
$this->load->view('templates/navbar', $vars);

// Sidebar
$this->load->view('templates/sidebar', $vars);

// Main Content
if (isset($content_view)) {
    $this->load->view($content_view, $vars);
}

// Footer
$this->load->view('templates/footer', $vars);
