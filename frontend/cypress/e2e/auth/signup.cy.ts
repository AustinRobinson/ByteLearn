import { faker } from '@faker-js/faker';

describe('Signup Page', () => {
    beforeEach(() => {
        // navigate to the signup page before each test
        cy.visit('/signup');
    });

    it('should display all required fields and the submit button', () => {
        // check for the presence of all input fields
        cy.get('[data-cy="first-name-input"]').should('exist');
        cy.get('[data-cy="last-name-input"]').should('exist');
        cy.get('[data-cy="username-input"]').should('exist');
        cy.get('[data-cy="email-input"]').should('exist');
        cy.get('[data-cy="password-input"]').should('exist');
        cy.get('[data-cy="confirm-password-input"]').should('exist');

        // check for the submit button
        cy.get('[data-cy="submit-button"]').should('exist').and('be.enabled');
    });

    it('should validate required fields', () => {
        // click the submit button without filling the form
        cy.get('[data-cy="submit-button"]').click();

        // verify error messages
        cy.get('[data-cy="first-name-required"]').should('contain', 'First name is required');
        cy.get('[data-cy="last-name-required"]').should('contain', 'Last name is required');
        cy.get('[data-cy="username-required"]').should('contain', 'Username is required');
        cy.get('[data-cy="email-required"]').should('contain', 'Email is required');
        cy.get('[data-cy="password-required"]').should('contain', 'Password is required');
        cy.get('[data-cy="confirm-password-match"]').should('contain', 'Confirm password must match password');
    });

    it('should validate email format', () => {
        // enter an invalid email
        cy.get('[data-cy="email-input"]').type('invalidemail');

        // click the submit button
        cy.get('[data-cy="submit-button"]').click();

        // verify the email format error
        cy.get('[data-cy="email-invalid"]').should('contain', 'Email invalid');
    });

    it('should validate password minimum length', () => {
        // enter a short password
        cy.get('[data-cy="password-input"]').type(faker.internet.password({ length: 7 }));

        // click the submit button
        cy.get('[data-cy="submit-button"]').click();

        // verify the password length error
        cy.get('[data-cy="password-minlength"]').should('contain', 'Password must be at least 8 characters long');
    });

    it('should validate password confirmation', () => {
        // enter password and mismatched confirmation password
        cy.get('[data-cy="password-input"]').type(faker.internet.password());
        cy.get('[data-cy="confirm-password-input"]').type(faker.internet.password());

        // click the submit button
        cy.get('[data-cy="submit-button"]').click();

        // verify the password confirmation error
        cy.get('[data-cy="confirm-password-match"]').should('contain', 'Confirm password must match password');
    });

    it('should display server-side validation errors', () => {
        // intercept the register API request
        cy.intercept('POST', '/api/register').as('registerRequest');

        // fill the form with data that passes client side validation
        // first name and last name should be under 255 characters
        cy.get('[data-cy="first-name-input"]').type("a".repeat(256));
        cy.get('[data-cy="last-name-input"]').type("a".repeat(256));
        cy.get('[data-cy="username-input"]').type('unametest');
        cy.get('[data-cy="email-input"]').type('testuser@example.com');
        cy.get('[data-cy="password-input"]').type('ValidPassword123');
        cy.get('[data-cy="confirm-password-input"]').type('ValidPassword123');

        // submit the form
        cy.get('[data-cy="submit-button"]').click();

        cy.wait('@registerRequest').then((interception) => {
            // assert the status code
            expect(interception?.response?.statusCode).to.eq(422);

            // get the actual response body
            const actualErrors = interception?.response?.body?.errors;

            // verify server-side validation messages are displayed dynamically
            cy.get('[data-cy="first-name-server-error"]').should('contain', actualErrors.first_name[0]);
            cy.get('[data-cy="last-name-server-error"]').should('contain', actualErrors.last_name[0]);
            cy.get('[data-cy="username-server-error"]').should('contain', actualErrors.username[0]);
            cy.get('[data-cy="email-server-error"]').should('contain', actualErrors.email[0]);
        });

    });

    it('should successfully submit the form with valid data', () => {
        // intercept the register API request
        cy.intercept('POST', '/api/register').as('registerRequest');

        // fill the form with valid data
        cy.get('[data-cy="first-name-input"]').type(faker.person.firstName());
        cy.get('[data-cy="last-name-input"]').type(faker.person.lastName());
        cy.get('[data-cy="username-input"]').type(faker.internet.userName());
        cy.get('[data-cy="email-input"]').type(faker.internet.email());
        const password = faker.internet.password();
        cy.get('[data-cy="password-input"]').type(password);
        cy.get('[data-cy="confirm-password-input"]').type(password);

        // submit the form
        cy.get('[data-cy="submit-button"]').click();

        // wait for the mocked response
        cy.wait('@registerRequest').its('response.statusCode').should('eq', 201);;

        // assert user is redirected to the login page
        cy.url().should('include', '/login');
    });
});
