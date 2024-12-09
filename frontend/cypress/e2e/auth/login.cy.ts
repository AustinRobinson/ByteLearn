import { mockLoginFailure, mockLoginSuccess, mockRefreshFailure } from "../../support/mockApi";

describe('Login Page', () => {
    beforeEach(() => {
        // navigate to the login page before each test
        cy.visit('/login');
    });

    it('should log in successfully with valid credentials', () => {
        // mock successful login response
        cy.mockApi(mockLoginSuccess);

        // input valid login credentials
        cy.get('[data-cy="email-input"]').type('testuser@example.com');
        cy.get('[data-cy="password-input"]').type('validpassword');
        cy.get('[data-cy="submit-button"]').click();

        // wait for mocked API response
        cy.wait('@loginRequest').its('response.statusCode').should('eq', 200);

        // assert user is redirected to the video feed
        cy.url().should('include', '/video-feed');
    });

    it('should display an error for invalid credentials', () => {
        // mock unsuccessful login response
        cy.mockApi(mockLoginFailure);

        // input invalid login credentials
        cy.get('[data-cy="email-input"]').type('wronguser@example.com');
        cy.get('[data-cy="password-input"]').type('wrongpassword');
        cy.get('[data-cy="submit-button"]').click();

        // wait for mocked API response
        cy.wait('@loginRequest').its('response.statusCode').should('eq', 401);

        // assert error message is displayed
        cy.get('[data-cy="form-error"]').should('contain', 'Email or password is incorrect');
    });
});
