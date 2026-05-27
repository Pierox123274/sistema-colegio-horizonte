describe('Gamification module', () => {
    it('redirects guest from student gamification route', () => {
        cy.visit('/student/gamification');
        cy.url().should('include', '/login');
    });
});

