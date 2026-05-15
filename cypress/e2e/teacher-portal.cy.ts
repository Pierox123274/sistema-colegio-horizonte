describe('Teacher portal routes', () => {
    it('redirects guests to login on teacher routes', () => {
        cy.visit('/teacher/dashboard', { failOnStatusCode: false });
        cy.url().should('include', '/login');

        cy.visit('/teacher/attendance', { failOnStatusCode: false });
        cy.url().should('include', '/login');

        cy.visit('/teacher/grades', { failOnStatusCode: false });
        cy.url().should('include', '/login');

        cy.visit('/teacher/students', { failOnStatusCode: false });
        cy.url().should('include', '/login');

        cy.visit('/teacher/reports', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });
});
