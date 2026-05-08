describe('Inventario', () => {
    it('redirige invitados al login en categorías', () => {
        cy.visit('/intranet/inventory/categories', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });

    it('redirige invitados al login en productos', () => {
        cy.visit('/intranet/inventory/products', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });

    it('redirige invitados al login en movimientos', () => {
        cy.visit('/intranet/inventory/movements', { failOnStatusCode: false });
        cy.url().should('include', '/login');
    });
});

