describe('Finanzas — comprobantes de pago', () => {
    it('redirige invitados al login en comprobante de pago', () => {
        cy.visit('/intranet/payments/1/receipt', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });

    it('redirige invitados al login en PDF', () => {
        cy.visit('/intranet/payments/1/receipt/pdf', {
            failOnStatusCode: false,
        });
        cy.url().should('include', '/login');
    });

    it('redirige invitados al login en ticket', () => {
        cy.visit('/intranet/payments/1/receipt/ticket', {
            failOnStatusCode: false,
        });
        cy.url().should('include', '/login');
    });
});

