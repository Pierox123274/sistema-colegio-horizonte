describe('IA avanzada — Copiloto docente', () => {
    it('carga el hub del copiloto para docente autenticado', () => {
        cy.loginAsTeacher();
        cy.visit('/teacher/ai-copilot');
        cy.contains('Copiloto pedagógico').should('be.visible');
        cy.contains('Generador de exámenes').should('be.visible');
    });

    it('muestra generador de exámenes', () => {
        cy.loginAsTeacher();
        cy.visit('/teacher/ai-copilot/exams');
        cy.contains('Generador de exámenes').should('be.visible');
        cy.get('input[placeholder*="fracciones"]').should('exist');
    });

    it('muestra generador de tareas', () => {
        cy.loginAsTeacher();
        cy.visit('/teacher/ai-copilot/assignments');
        cy.contains('Generador de tareas').should('be.visible');
    });
});

describe('IA avanzada — Tutor estudiante', () => {
    it('muestra panel coach de aprendizaje', () => {
        cy.loginAsStudent();
        cy.visit('/student/ai-tutor');
        cy.contains('Coach de aprendizaje').should('be.visible');
        cy.contains('Resumen').should('be.visible');
    });
});
