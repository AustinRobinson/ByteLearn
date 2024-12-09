import { faker } from "@faker-js/faker";

describe('Login Page', () => {
    beforeEach(() => {
        // navigate to the login page before each test
        cy.clearAllCookies();
        cy.visit('/login');
    });

    it('should log in successfully with valid credentials', () => {
        // intercept the login API request
        cy.intercept('POST', '/api/login').as('loginRequest');

        // input valid login credentials
        cy.get('[data-cy="email-input"]').type('testuser@example.com');
        cy.get('[data-cy="password-input"]').type('validpassword');
        cy.get('[data-cy="submit-button"]').click();

        cy.wait('@loginRequest')
            .its('response')
            .then((response) => {
                // check the status code
                expect(response.statusCode).to.eq(200);

                // check the access token in the response body
                expect(response.body).to.have.property('access_token');
                expect(response.body.access_token).to.not.be.empty;
            });

        // assert user is redirected to the video feed
        cy.url().should('include', '/video-feed');

        // click the logout button
        cy.get('[data-cy="profile-photo"]').click();
        cy.get('[data-cy="logout-button-desktop"]').click();

        // assert user is redirected to the home page
        cy.url().should('include', '/');
    });

    it('should display an error for invalid credentials', () => {
        // intercept the login API request
        cy.intercept('POST', '/api/login').as('loginRequest');

        // input invalid login credentials
        cy.get('[data-cy="email-input"]').type(faker.internet.email());
        cy.get('[data-cy="password-input"]').type(faker.internet.password());
        cy.get('[data-cy="submit-button"]').click();

        // wait for mocked API response
        cy.wait('@loginRequest').its('response.statusCode').should('eq', 401);

        // assert error message is displayed
        cy.get('[data-cy="form-error"]').should('contain', 'Email or password is incorrect');
    });
});
