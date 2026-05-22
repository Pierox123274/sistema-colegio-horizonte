/**
 * Fase 23 — Aula virtual / LMS (smoke): rutas protegidas redirigen a login sin sesión.
 */
describe('Virtual classroom routes (guest)', () => {
    it('redirects teacher classrooms index to login', () => {
        cy.visit('/teacher/classrooms', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });

    it('redirects student classrooms index to login', () => {
        cy.visit('/student/classrooms', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });

    it('redirects teacher calendar to login', () => {
        cy.visit('/teacher/calendar', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });

    it('redirects student calendar to login', () => {
        cy.visit('/student/calendar', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });

    it('redirects intranet LMS overview to login', () => {
        cy.visit('/intranet/lms', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });
});
