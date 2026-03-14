<form action="" id="loginForm" autocomplete="off">
    @csrf

    <!-- Email -->
    <div class="form-floating mb-3">
        <input type="text"
            id="input-email"
            name="login_field"
            class="form-control rounded-4"
            placeholder="Email">
        <label for="input-email">Email <span class="text-danger">*</span></label>
    </div>

    <!-- Password -->
    <div class="form-floating mb-3 position-relative">
        <input type="password"
            id="input-password"
            name="password"
            class="form-control rounded-4"
            placeholder="Password">

        <label for="input-password">Password <span class="text-danger">*</span></label>

        <!-- Password toggle icon -->
        <span id="btn-view-password"
            class="position-absolute top-50 end-0 translate-middle-y me-3"
            style="cursor: pointer;">
            <i class="fa fa-eye"></i>
        </span>
    </div>

    <!-- Captcha -->
    <div class="mb-3">
        <div class="captcha mb-2 d-flex align-items-center gap-2" id="captcha">
            <img src="{{ captcha_src() }}" alt="CAPTCHA image" class="rounded" style="height: 40px;">
            <button type="button"
                id="refresh-captcha"
                class="btn btn-secondary rounded-4"
                aria-label="Refresh Captcha">
                <i class="fa fa-sync"></i>
            </button>
        </div>

        <div class="form-floating">
            <input type="text"
                id="input-captcha"
                name="captcha"
                class="form-control rounded-4"
                placeholder="Enter captcha code">
            <label for="input-captcha">Captcha <span class="text-danger">*</span></label>
        </div>
    </div>

    <!-- Forgot password -->
    <div class="mb-3">
        <a href="{{ route('public.login.forget-password.index') }}" class="text-primary">
            <i class="fa fa-lock"></i> Forgot Password?
        </a>
    </div>

    <!-- Submit button -->
    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-custom py-4">
            <i class="fa fa-paper-plane"></i> Login
        </button>
    </div>

    <div class="mb-3 text-center">
        <p>Or</p>
    </div>

    <!-- Google Sign In -->
    <div class="mb-3 text-center">
        <a href="{{ route('auth.google') }}?origin=checkout" class="btn btn-outline-danger w-100 rounded-4 py-2">
            <i class="fab fa-google me-2"></i> Sign in with Google
        </a>
    </div>
</form>