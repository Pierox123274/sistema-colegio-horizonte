describe('Integraciones externas', () => {
    it('carga el panel de integraciones para admin', () => {
        cy.loginAsAdmin();
        cy.visit('/intranet/integrations');
        cy.contains('Integraciones institucionales').should('be.visible');
        cy.contains('Google Calendar').should('be.visible');
    });

    it('muestra health checks', () => {
        cy.loginAsAdmin();
        cy.visit('/intranet/integrations');
        cy.contains('Health checks').should('be.visible');
        cy.contains('SMTP').should('be.visible');
    });
});
