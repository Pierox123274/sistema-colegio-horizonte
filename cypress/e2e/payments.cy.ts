describe('Finanzas — pagos (base)', () => {
    it('redirige invitados desde el listado de pagos al login', () => {
        cy.visit('/intranet/payments');
        cy.url().should('include', '/login');
    });
});
