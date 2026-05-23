describe('CMS institucional', () => {
    it('carga la web pública con noticias del CMS', () => {
        cy.visit('/noticias');
        cy.contains('Noticias').should('be.visible');
        cy.contains('admisión', { matchCase: false }).should('exist');
    });
});
