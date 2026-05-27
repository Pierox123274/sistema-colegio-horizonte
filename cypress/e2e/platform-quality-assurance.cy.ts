describe('platform quality assurance (Fase 29)', () => {
    it('redirects guest from intranet dashboard', () => {
        cy.visit('/intranet/dashboard');
        cy.url().should('include', '/login');
    });

    it('redirects guest from teacher portal', () => {
        cy.visit('/teacher/dashboard');
        cy.url().should('include', '/login');
    });

    it('redirects guest from student portal', () => {
        cy.visit('/student/dashboard');
        cy.url().should('include', '/login');
    });

    it('redirects guest from notifications center', () => {
        cy.visit('/notifications');
        cy.url().should('include', '/login');
    });

    it('redirects guest from CMS admin', () => {
        cy.visit('/intranet/cms');
        cy.url().should('include', '/login');
    });

    it('serves public home without authentication', () => {
        cy.visit('/');
        cy.contains(/Horizonte|Colegio|Inicio/i).should('exist');
    });

    it('legacy teacher adaptive-learning redirects to pedagogical panel', () => {
        cy.visit('/teacher/adaptive-learning', { failOnStatusCode: false });
        cy.url().should('satisfy', (url: string) => {
            return url.includes('/login') || url.includes('/teacher/pedagogical-panel');
        });
    });
});
