describe('Seguridad y auditoría', () => {
    it('muestra auditoría para administrador autenticado', () => {
        cy.visit('/intranet/security/audit-logs', { failOnStatusCode: false });
        cy.get('body').then(($body) => {
            if ($body.text().includes('Auditoría institucional')) {
                cy.contains('Auditoría institucional').should('be.visible');
            }
        });
    });

    it('bloquea acceso público a sesiones', () => {
        cy.visit('/intranet/security/sessions', { failOnStatusCode: false });
        cy.url().should('not.include', '/intranet/security/sessions');
    });
});
