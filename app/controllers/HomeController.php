<?php
/**
 * Home Controller
 * Manages Landing Page, About Us, and Contact Us views
 */

class HomeController extends Controller {

    public function index() {
        $data = [
            'pageTitle' => 'AuraCraft | Premium On-Demand Home Services & Master Craftsmen',
            'activePage' => 'home'
        ];
        $this->view('home/index', $data);
    }

    public function about() {
        $data = [
            'pageTitle' => 'About Us | The AuraCraft Standard of Craftsmanship',
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
