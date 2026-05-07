describe('Matrículas (base)', () => {
    it('redirige invitados desde el listado de matrículas al login', () => {
        cy.visit('/intranet/enrollments');
        cy.url().should('include', '/login');
    });
});
