import { fa, faker } from "@faker-js/faker";

describe('Video Feed', () => {
    beforeEach(() => {
        // navigate to the login page
        cy.clearAllCookies();
        cy.visit('/login');

        // input valid login credentials
        cy.get('[data-cy="email-input"]').type('testuser@example.com');
        cy.get('[data-cy="password-input"]').type('validpassword');
        cy.get('[data-cy="submit-button"]').click();

        // assert user is redirected to the video feed
        cy.url().should('include', '/video-feed');
    });

    it('should display the video player', () => {
        // verify that the video player is visible
        cy.get('[data-cy="video-player"]').should('be.visible');
    });

    it('should toggle video details accordion', () => {
        cy.get('[data-cy="details-content"]').then(($detailsContent) => {
            // check if the details accordion is currently visible
            const isVisible = $detailsContent.is(':visible');

            // if it is open, close it
            if (!isVisible) {
                cy.get('[data-cy="details-toggle"]').click();
                cy.get('[data-cy="details-content"]').should('be.visible');
            }

            // ensure it can be closed again
            cy.get('[data-cy="details-toggle"]').click();
            cy.get('[data-cy="details-content"]').should('not.exist');

            // ensure it can be opened
            cy.get('[data-cy="details-toggle"]').click();
            cy.get('[data-cy="details-content"]').should('be.visible');


        });
    });

    it('should display video details correctly', () => {
        // verify video details accordion content
        cy.get('[data-cy="video-title"]').should('not.be.empty');
        cy.get('[data-cy="video-creator"]').should('not.be.empty');
        cy.get('[data-cy="video-date"]').should('not.be.empty');
    });

    it('should toggle comments accordion', () => {
        cy.get('[data-cy="comments-content"]').then(($commentsContent) => {
            // check if the comments accordion is currently visible
            const isVisible = $commentsContent.is(':visible');

            // ensure it can be closed
            if (!isVisible) {
                cy.get('[data-cy="comments-toggle"]').click();
                cy.get('[data-cy="comments-content"]').should('be.visible');
            }

            // ensure it can be closed again
            cy.get('[data-cy="comments-toggle"]').click();
            cy.get('[data-cy="comments-content"]').should('not.exist');

            // ensure it can be opened
            cy.get('[data-cy="comments-toggle"]').click();
            cy.get('[data-cy="comments-content"]').should('be.visible');
        });
    });

    it('should validate the comment form', () => {
        // open comments accordion and validate the form
        cy.get('[data-cy="comment-form"]').within(() => {
            cy.get('[data-cy="comment-button"]').should('be.disabled');
            cy.get('[data-cy="comment-input"]').clear().type(faker.lorem.sentence());
            cy.get('[data-cy="comment-button"]').should('not.be.disabled');
        });
    });

    it('should clear the comment form', () => {
        // open comments accordion and validate the form
        cy.get('[data-cy="comment-form"]').within(() => {
            cy.get('[data-cy="comment-button"]').should('be.disabled');
            cy.get('[data-cy="comment-input"]').clear().type(faker.lorem.sentence());
            cy.get('[data-cy="clear-button"]').click();
            cy.get('[data-cy="comment-input"]').should('be.empty');
        });
    });

    it('should post a comment and display it', () => {
        // add and verify a comment
        const commentText = faker.lorem.sentence();
        cy.get('[data-cy="comment-form"]').within(() => {
            cy.get('[data-cy="comment-input"]').type(commentText);
            cy.get('[data-cy="comment-button"]').click();
        });
        cy.get('[data-cy="comment-item"]').should('contain', commentText);
    });

    it('should like and unlike a comment', () => {
        // like and unlike the first comment
        cy.get('[data-cy="comment-item"]').first().within(() => {
            cy.get('[data-cy="comment-like-button"]').click();
            cy.get('[data-cy="comment-like-count"]').should('contain', '1');
            cy.get('[data-cy="comment-like-button"]').click();
            cy.get('[data-cy="comment-like-count"]').should('contain', '0');
        });
    });

    it('should like and unlike the video', () => {
        // like and unlike the video
        cy.get('[data-cy="like-button"]').click();
        cy.get('[data-cy="like-button"]').should('have.class', 'text-red-500');
        cy.get('[data-cy="like-button"]').click();
        cy.get('[data-cy="like-button"]').should('have.class', 'text-gray-500');
    });
});
