/**
 * Fase 21 — Tutor IA (smoke): rutas protegidas redirigen a login sin sesión.
 */
describe('AI tutor routes (guest)', () => {
    it('redirects student AI tutor to login', () => {
        cy.visit('/student/ai-tutor', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });

    it('redirects intranet AI analytics to login', () => {
        cy.visit('/intranet/ai-analytics', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });
});
