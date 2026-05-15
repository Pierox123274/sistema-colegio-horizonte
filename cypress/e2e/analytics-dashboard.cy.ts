describe('Analytics dashboard routes', () => {
    it('redirects guests to login on analytics routes', () => {
        cy.visit('/intranet/analytics', { failOnStatusCode: false });
        cy.url().should('include', '/login');

        cy.visit('/intranet/reports/analytics', { failOnStatusCode: false });
        cy.url().should('include', '/login');

        cy.visit('/teacher/analytics', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });
});
