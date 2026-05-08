describe('attendance routes', () => {
    it('redirects guests to login', () => {
        cy.visit('/intranet/attendance');
        cy.url().should('include', '/login');
    });

    it('redirects guests from attendance create to login', () => {
        cy.visit('/intranet/attendance/create');
        cy.url().should('include', '/login');
    });
});

