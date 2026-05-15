describe('Academic grades intranet routes', () => {
    it('redirects guests to login on grades routes', () => {
        cy.visit('/intranet/academic/subjects', { failOnStatusCode: false });
        cy.url().should('include', '/login');

        cy.visit('/intranet/academic/evaluations', { failOnStatusCode: false });
        cy.url().should('include', '/login');

        cy.visit('/intranet/academic/grades/records', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });
});

