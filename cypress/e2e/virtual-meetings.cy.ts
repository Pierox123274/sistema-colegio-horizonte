describe('virtual meetings access', () => {
    it('redirects guest from teacher meetings', () => {
        cy.visit('/teacher/meetings');
        cy.url().should('include', '/login');
    });

    it('redirects guest from student meetings', () => {
        cy.visit('/student/meetings');
        cy.url().should('include', '/login');
    });

    it('redirects guest from admin meetings panel', () => {
        cy.visit('/intranet/meetings');
        cy.url().should('include', '/login');
    });
});
