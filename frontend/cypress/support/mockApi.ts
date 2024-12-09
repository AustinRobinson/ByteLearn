// API mock responses

export const mockLoginSuccess = {
    method: 'POST',
    url: '/api/login',
    statusCode: 200,
    response: {
        access_token: 'valid_access_token',
    },
};

export const mockLoginFailure = {
    method: 'POST',
    url: '/api/login',
    statusCode: 401,
    response: {
        message: 'Invalid credentials',
    },
};

export const mockRefreshSuccess = {
    method: 'POST',
    url: '/api/refresh',
    statusCode: 200,
    response: {
        access_token: 'valid_access_token',
    },
};

export const mockRefreshFailure = {
    method: 'POST',
    url: '/api/refresh',
    statusCode: 401,
    response: {
        message: 'Invalid or expired refresh token',
    },
};
