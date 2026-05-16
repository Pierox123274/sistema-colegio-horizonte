describe('DevOps — panel de sistema', () => {
    it('redirige o deniega acceso público a salud del sistema', () => {
        cy.visit('/intranet/system/health', { failOnStatusCode: false });
        cy.url().should('not.include', '/intranet/system/health');
    });
});
