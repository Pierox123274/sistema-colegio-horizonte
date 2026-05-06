describe('Estudiantes (base)', () => {
    it('redirige invitados desde el listado de estudiantes al login', () => {
        cy.visit('/intranet/students');
        cy.url().should('include', '/login');
    });
});
