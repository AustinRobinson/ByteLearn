// API mock responses

// mocks login success
export const mockLoginSuccess = {
    method: 'POST',
    url: '/api/login',
    statusCode: 200,
    response: {
        access_token: 'valid_access_token',
    },
};

// mocks login failure
export const mockLoginFailure = {
    method: 'POST',
    url: '/api/login',
    statusCode: 401,
    response: {
        message: 'Invalid credentials',
    },
};

// mocks access token refresh success
export const mockRefreshSuccess = {
    method: 'POST',
    url: '/api/refresh',
    statusCode: 200,
    response: {
        access_token: 'valid_access_token',
    },
};


// mocks access token refresh failure
export const mockRefreshFailure = {
    method: 'POST',
    url: '/api/refresh',
    statusCode: 401,
    response: {
        message: 'Invalid or expired refresh token',
    },
};

// mocks registration success
export const mockRegisterSuccess = {
    method: 'POST',
    url: '/api/register',
    statusCode: 201,
    response: {},
};

// mocks server side validation messages
export const mockRegisterValidationFailure = {
    method: 'POST',
    url: '/api/register',
    statusCode: 400,
    response: {
        message: "The first name field is required. (and 5 more errors)",
        errors: {
            first_name: [
                "The first name field is required."
            ],
            last_name: [
                "The last name field is required."
            ],
            username: [
                "The username has already been taken."
            ],
            email: [
                "The email field must be a valid email address."
            ],
            password: [
                "The password field must be at least 8 characters.",
                "The password field confirmation does not match."
            ],
            password_confirmation: [
                "The passwords do not match."
            ]
        }
    },
};
