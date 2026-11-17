<?php
/**
 * Home Controller
 * Manages Landing Page, About Us, and Contact Us views
 */

class HomeController extends Controller {

    public function index() {
        $data = [
            'pageTitle' => 'NEARZO | Nearby Verified Home & Living Services',
            'activePage' => 'home'
        ];
        $this->view('home/index', $data);
    }

    public function about() {
        $data = [
            'pageTitle' => 'About Us | The NEARZO Standard of Service',
            'activePage' => 'about'
        ];
        $this->view('home/about', $data);
    }

    public function contact() {
        $data = [
            'pageTitle' => 'Contact Us | Concierge Client & Partner Support',
            'activePage' => 'contact'
        ];
        $this->view('home/contact', $data);
    }
}
