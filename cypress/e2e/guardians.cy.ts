describe('Apoderados (base)', () => {
    it('redirige invitados desde el listado de apoderados al login', () => {
        cy.visit('/intranet/guardians');
        cy.url().should('include', '/login');
    });
});
