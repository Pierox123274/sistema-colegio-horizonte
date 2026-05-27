# Diagrama — Arquitectura general del sistema

```mermaid
flowchart TB
    subgraph Clients["Clientes"]
        PUB[Sitio público]
        INT[Intranet Admin/Secretaría]
        TCH[Portal Docente]
        STU[Portal Estudiante]
    end

    subgraph App["Laravel + Inertia"]
        WEB[routes/web.php]
        WH[webhooks]
        CTRL[Controllers]
        SVC[Services]
        POL[Policies]
    end

    subgraph Providers["Proveedores desacoplados"]
        AI[app/AI]
        MTG[app/Meetings]
        INTG[app/Integrations]
    end

    DB[(MySQL)]
    QUEUE[(Cola / Redis)]
    STORAGE[(Storage)]

    PUB --> WEB
    INT --> WEB
    TCH --> WEB
    STU --> WEB
    WEB --> CTRL
    WH --> CTRL
    CTRL --> POL
    CTRL --> SVC
    SVC --> AI
    SVC --> MTG
    SVC --> INTG
    SVC --> DB
    SVC --> QUEUE
    SVC --> STORAGE
```

## Descripción

Los cuatro canales de usuario convergen en una aplicación monolítica modular. Los proveedores externos se invocan solo desde servicios, nunca desde controladores directamente.
