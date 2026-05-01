describe('Autenticación (base)', () => {
    it('muestra la pantalla de login', () => {
        cy.visit('/login');
        cy.contains('Log in').should('exist');
    });

    it('redirige invitados desde la intranet al login', () => {
        cy.visit('/intranet/dashboard');
        cy.url().should('include', '/login');
    });
});
