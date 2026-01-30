# 📦 Esku

Sistema de **gestión de inventario** universal (multisector: retail, hostelería, almacenes).

## 🚀 Inicio Rápido

1. **Inicializar el back**

```bash
cp .env.example .env
make build
make up
```

2. **Inicializar el frontal**

```bash
cd frontend/
npm i
ng server
```

## 🌐 Accesos a los Servicios

Una vez levantados los entornos, estos son los puntos de acceso locales:

| Servicio            | URL / Host                                                       | Puerto | Descripción                                      |
| :------------------ | :--------------------------------------------------------------- | :----- | :----------------------------------------------- |
| **Frontend**        | [http://localhost:4200](http://localhost:4200)                   | `4200` | Angular                                          |
| **Front > Backend** | [http://localhost:4200/api/test](http://localhost:4200/api/test) | `4200` | Symfony API                                      |
| **Backend**         | [http://localhost:8080](http://localhost:8080)                   | `8080` | Symfony API                                      |
| **Mailpit**         | [http://localhost:8025](http://localhost:8025)                   | `8025` | Panel de control de correos (Entorno de pruebas) |
| **Base de Datos**   | `localhost`                                                      | `5432` | PostgreSQL (DBeaver)                             |

>El proxy `proxy.conf.json` solo funciona en desarrollo con el servidor de Angular. En producción, será el propio servidor web quien haga de pasarela enviando las peticiones del frontend al backend.


## 🤖 Configuración de IA

Este proyecto utiliza un archivo de reglas específico para asegurar la consistencia del código. Importante: Antes de pedir código a una IA, asegúrate de que lea el archivo "context.md" añadiendo el siguiente comando en el chat de tu IA para cargar el contexto:

> Contexto: @context.md
