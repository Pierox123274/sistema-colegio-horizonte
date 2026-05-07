describe('Estructura académica (base)', () => {
    it('redirige invitados desde niveles al login', () => {
        cy.visit('/intranet/academic/levels');
        cy.url().should('include', '/login');
    });

    it('redirige invitados desde grados al login', () => {
        cy.visit('/intranet/academic/grades');
        cy.url().should('include', '/login');
    });

    it('redirige invitados desde secciones al login', () => {
        cy.visit('/intranet/academic/sections');
        cy.url().should('include', '/login');
    });

    it('redirige invitados desde aulas al login', () => {
        cy.visit('/intranet/academic/classrooms');
        cy.url().should('include', '/login');
    });
});
