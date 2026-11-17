<?php
/**
 * Auth Controller
 * Full authentication handling: Login verification, registration, password hashing,
 * CSRF validation, role-based redirection, and session security.
 */

class AuthController extends Controller {

    /**
     * Show Sign In Page
     */
    public function login() {
        // If already logged in, redirect to respective dashboard
        if ($this->currentUser()) {
            $this->redirectRoleDashboard($this->currentUser()['role']);
        }

        $data = [
            'pageTitle'  => 'Sign In | AuraCraft On-Demand',
            'activePage' => 'auth'
        ];
        $this->view('auth/login', $data);
    }

    /**
     * Process Sign In Form Submission
     */
    public function loginSubmit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/login');
        }

        if (!$this->validateCsrf()) {
            $this->redirect('auth/login');
        }

        $email    = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['flash_error'] = "Please provide both email address and password.";
            $this->redirect('auth/login');
        }

        /** @var User $userModel */
        $userModel = $this->model('User');
        $user = $userModel->findByEmail($email);

        if (!$user || !$userModel->verifyPassword($password, $user['password_hash'])) {
            $_SESSION['flash_error'] = "Invalid credentials. Please verify your email and password.";
            $this->redirect('auth/login');
        }

        if ($user['status'] !== 'active') {
            $_SESSION['flash_error'] = "Your account is currently " . htmlspecialchars($user['status']) . ". Please contact support.";
            $this->redirect('auth/login');
        }

        // Prevent session fixation attack
        session_regenerate_id(true);

        // Store user in session
        $_SESSION['user'] = [
            'id'        => (int)$user['id'],
            'full_name' => $user['full_name'],
            'email'     => $user['email'],
            'phone'     => $user['phone'],
            'role'      => $user['role'],
            'avatar'    => $user['avatar']
        ];

        // Prompt provider for shift availability upon sign in
        if ($user['role'] === 'provider') {
            $_SESSION['prompt_availability'] = true;
            setcookie('prompt_avail', '1', time() + 3600, '/');
        }

        $_SESSION['flash_success'] = "Welcome back, " . htmlspecialchars($user['full_name']) . "!";
        $this->redirectRoleDashboard($user['role']);
    }

    /**
     * Show Choose Account Type Page (Customer vs Provider)
     */
    public function register() {
        if ($this->currentUser()) {
            $this->redirectRoleDashboard($this->currentUser()['role']);
        }

        $data = [
            'pageTitle'  => 'Join AuraCraft | Choose Account Type',
            'activePage' => 'auth'
        ];
        $this->view('auth/register_choose', $data);
    }

    /**
     * Show Customer Registration Form
     */
    public function registerCustomer() {
        if ($this->currentUser()) {
            $this->redirectRoleDashboard($this->currentUser()['role']);
        }

        $data = [
            'pageTitle'  => 'Create Customer Account | AuraCraft',
            'activePage' => 'auth'
        ];
        $this->view('auth/register_customer', $data);
    }

    /**
     * Process Customer Registration
     */
    public function registerCustomerSubmit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/registerCustomer');
        }

        if (!$this->validateCsrf()) {
            $this->redirect('auth/registerCustomer');
        }

        $fullName   = trim($_POST['full_name'] ?? '');
        $email      = strtolower(trim($_POST['email'] ?? ''));
        $phone      = trim($_POST['phone'] ?? '');
        $address    = trim($_POST['address'] ?? '');
        $city       = trim($_POST['city'] ?? 'San Francisco');
        $postalCode = trim($_POST['postal_code'] ?? '');
        $password   = $_POST['password'] ?? '';
        $confirm    = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($fullName) || empty($email) || empty($phone) || empty($password)) {
            $_SESSION['flash_error'] = "Please fill in all mandatory fields.";
            $this->redirect('auth/registerCustomer');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = "Please enter a valid email address.";
            $this->redirect('auth/registerCustomer');
        }

        if (strlen($password) < 8) {
            $_SESSION['flash_error'] = "Password must be at least 8 characters long.";
            $this->redirect('auth/registerCustomer');
        }

        if ($password !== $confirm) {
            $_SESSION['flash_error'] = "Password and confirmation do not match.";
            $this->redirect('auth/registerCustomer');
        }

        /** @var User $userModel */
        $userModel = $this->model('User');
        if ($userModel->emailExists($email)) {
            $_SESSION['flash_error'] = "An account with this email address already exists. Please sign in.";
            $this->redirect('auth/login');
        }

        // Create User & Customer Profile
        try {
            $userId = $userModel->create([
                'full_name' => $fullName,
                'email'     => $email,
                'phone'     => $phone,
                'password'  => $password,
                'role'      => 'customer',
                'status'    => 'active'
            ]);

            /** @var Customer $customerModel */
            $customerModel = $this->model('Customer');
            $customerModel->create($userId, [
                'address'     => $address,
                'city'        => $city,
                'postal_code' => $postalCode
            ]);

            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'        => $userId,
                'full_name' => $fullName,
                'email'     => $email,
                'phone'     => $phone,
                'role'      => 'customer',
                'avatar'    => null
            ];

            $_SESSION['flash_success'] = "Welcome to AuraCraft! Your customer account has been created.";
            $this->redirect('customer/dashboard');

        } catch (Throwable $e) {
            $_SESSION['flash_error'] = "Registration error: " . $e->getMessage();
            $this->redirect('auth/registerCustomer');
        }
    }

    /**
     * Show Provider Registration Form
     */
    public function registerProvider() {
        if ($this->currentUser()) {
            $this->redirectRoleDashboard($this->currentUser()['role']);
        }

        $data = [
            'pageTitle'  => 'Apply as Service Partner | AuraCraft',
            'activePage' => 'auth'
        ];
        $this->view('auth/register_provider', $data);
    }

    /**
     * Process Provider Registration
     */
    public function registerProviderSubmit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/registerProvider');
        }

        if (!$this->validateCsrf()) {
            $this->redirect('auth/registerProvider');
        }

        $fullName     = trim($_POST['full_name'] ?? '');
        $businessName = trim($_POST['business_name'] ?? '');
        $profession   = trim($_POST['profession'] ?? 'Plumber');
        $email        = strtolower(trim($_POST['email'] ?? ''));
        $phone        = trim($_POST['phone'] ?? '');
        $rate         = (float)($_POST['hourly_rate'] ?? 499.00);
        $priceType    = trim($_POST['price_type'] ?? 'per_hour');
        $availStatus  = trim($_POST['availability_status'] ?? 'available');
        $experience   = (int)($_POST['experience_years'] ?? 1);
        $radius       = (int)($_POST['service_radius_km'] ?? 15);
        $address      = trim($_POST['address'] ?? 'Mumbai');
        $latitude     = (float)($_POST['latitude'] ?? 19.06000000);
        $longitude    = (float)($_POST['longitude'] ?? 72.86500000);
        $avatar       = trim($_POST['avatar'] ?? '👨‍🔧');
        $bio          = trim($_POST['bio'] ?? '');
        $password     = $_POST['password'] ?? '';
        $confirm      = $_POST['confirm_password'] ?? '';

        if (empty($fullName) || empty($businessName) || empty($email) || empty($phone) || empty($password)) {
            $_SESSION['flash_error'] = "Please fill in all mandatory provider application fields.";
            $this->redirect('auth/registerProvider');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = "Invalid email address.";
            $this->redirect('auth/registerProvider');
        }

        if (strlen($password) < 6 || $password !== $confirm) {
            $_SESSION['flash_error'] = "Passwords must match and be at least 6 characters.";
            $this->redirect('auth/registerProvider');
        }

        /** @var User $userModel */
        $userModel = $this->model('User');
        if ($userModel->emailExists($email)) {
            $_SESSION['flash_error'] = "An account with this email is already registered.";
            $this->redirect('auth/login');
        }

        try {
            $userId = $userModel->create([
                'full_name' => $fullName,
                'email'     => $email,
                'phone'     => $phone,
                'password'  => $password,
                'role'      => 'provider',
                'status'    => 'active',
                'avatar'    => $avatar
            ]);

            /** @var Provider $providerModel */
            $providerModel = $this->model('Provider');
            $providerModel->create($userId, [
                'business_name'       => $businessName,
                'profession'          => $profession,
                'bio'                 => $bio,
                'hourly_rate'         => $rate,
                'price_type'          => $priceType,
                'experience_years'    => $experience,
                'service_radius_km'   => $radius,
                'address'             => $address,
                'city'                => 'Mumbai',
                'state'               => 'MH',
                'postal_code'         => '400050',
                'latitude'            => $latitude,
                'longitude'           => $longitude,
                'availability_status' => $availStatus,
                'profile_photo'       => $avatar,
                'show_phone'          => 1
            ]);

            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'        => $userId,
                'full_name' => $fullName,
                'email'     => $email,
                'phone'     => $phone,
                'role'      => 'provider',
                'avatar'    => $avatar
            ];

            $_SESSION['flash_success'] = "Partner account registered successfully! Welcome to the marketplace, " . htmlspecialchars($fullName) . ".";
            $this->redirect('provider/dashboard');

        } catch (Throwable $e) {
            $_SESSION['flash_error'] = "Error submitting application: " . $e->getMessage();
            $this->redirect('auth/registerProvider');
        }
    }

    /**
     * Sign Out
     */
    public function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        session_start();

        $_SESSION['flash_success'] = "You have signed out successfully.";
        $this->redirect('');
    }

    /**
     * Helper to route user to correct dashboard
     */
    private function redirectRoleDashboard(string $role) {
        if ($role === 'provider') {
            $this->redirect('provider/dashboard');
        } elseif ($role === 'admin') {
            $this->redirect('admin/dashboard');
        } else {
            $this->redirect('customer/dashboard');
        }
    }
}
