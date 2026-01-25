# 📦 Esku

Sistema de **gestión de inventario** universal (multisector: retail, hostelería, almacenes).

## 🚀 Inicio Rápido

1. **Levantar el entorno:**

```bash
cp .env.example .env
make build
make up
```

## 🌐 Accesos a los Servicios

Una vez ejecutado `make up`, estos son los puntos de acceso locales:

| Servicio                | URL / Host                                                       | Puerto | Descripción                                      |
| :---------------------- | :--------------------------------------------------------------- | :----- | :----------------------------------------------- |
| **Frontend**            | [http://localhost:5173](http://localhost:5173)                   | `5173` | Vue 3                                            |
| **Frontend -> Backend** | [http://localhost:5173/api/test](http://localhost:5173/api/test) | `5173` | Symfony API                                      |
| **Backend**             | [http://localhost:8080](http://localhost:8080)                   | `8080` | Symfony API                                      |
| **Mailpit**             | [http://localhost:8025](http://localhost:8025)                   | `8025` | Panel de control de correos (Entorno de pruebas) |
| **Base de Datos**       | `localhost`                                                      | `5432` | PostgreSQL (DBeaver)                             |

## 🤖 Configuración de IA

Este proyecto utiliza un archivo de reglas específico para asegurar la consistencia del código. Importante: Antes de pedir código a una IA, asegúrate de que lea el archivo "context.md" añadiendo el siguiente comando en el chat de tu IA para cargar el contexto:

> Contexto: @context.md
