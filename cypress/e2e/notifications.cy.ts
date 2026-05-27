describe('notifications access', () => {
    it('redirects guest from notifications center', () => {
        cy.visit('/notifications');
        cy.url().should('include', '/login');
    });

    it('redirects guest from notification settings', () => {
        cy.visit('/settings/notifications');
        cy.url().should('include', '/login');
    });
});
