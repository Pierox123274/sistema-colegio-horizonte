/**
 * Fase 22 — Aprendizaje adaptativo (smoke): rutas protegidas redirigen a login sin sesión.
 */
describe('Adaptive learning routes (guest)', () => {
    it('redirects student diagnostic index to login', () => {
        cy.visit('/student/diagnostic', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });

    it('redirects student learning path to login', () => {
        cy.visit('/student/learning-path', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });

    it('redirects teacher adaptive learning to login', () => {
        cy.visit('/teacher/adaptive-learning', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });

    it('redirects teacher diagnostic results to login', () => {
        cy.visit('/teacher/diagnostic-results', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });

    it('redirects intranet adaptive analytics to login', () => {
        cy.visit('/intranet/adaptive-analytics', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });
});
