# Conclusión para el diseño de botones (futuro)

**Siempre que se agregue un nuevo botón al sistema:**

- Usar clases personalizadas y/o de framework (por ejemplo, `.daisy-btn`, `.daisy-btn-accent`) para todos los botones.
- Asegurarse de que el CSS esté correctamente cerrado y sin errores de sintaxis.
- Verificar que la clase tenga reglas de fondo, color y borde bien definidas y visibles.
- Probar visualmente cada botón en el navegador tras cada cambio.
- Si un botón no muestra el estilo esperado, revisar:
  - Que la clase esté bien escrita en el HTML.
  - Que el CSS esté bien enlazado y sin errores.
  - Que no haya reglas fuera de bloque o con llaves mal cerradas.
  - Que la especificidad sea suficiente para sobrescribir estilos de Bootstrap u otros frameworks.
- Documentar en el archivo MAPA_CSS_LOGIN.md o en este documento la clase y el archivo CSS/SCSS que controla cada botón nuevo.

**Esto evitará confusiones y pérdida de tiempo en el futuro.**

---

_Actualizado: 27 de diciembre de 2025_
