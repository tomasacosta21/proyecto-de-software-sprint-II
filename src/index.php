<h2>Lista de Usuarios</h2>
<table border="1">
<tr>
  <th>ID</th><th>Nombre</th><th>Apellido</th><th>Nickname</th><th>Email</th><th>Rol</th><th>Acciones</th>
</tr>
<?php foreach($usuarios as $u): ?>
<tr>
  <td><?= htmlspecialchars($u['id_usuario']) ?></td>
  <td><?= htmlspecialchars($u['nombre']) ?></td>
  <td><?= htmlspecialchars($u['apellido']) ?></td>
  <td><?= htmlspecialchars($u['nickname']) ?></td>
  <td><?= htmlspecialchars($u['email']) ?></td>
  <td><?= htmlspecialchars($u['rol']) ?></td>
  <td>
    <a href="editar.php?id=<?= $u['id_usuario'] ?>">Editar</a> |
    <a href="eliminar.php?id=<?= $u['id_usuario'] ?>" onclick="return confirm('¿Seguro que quieres eliminar este usuario?')">Eliminar</a>
  </td>
</tr>
<?php endforeach; ?>
</table>
<a href="crear.php">➕ Crear nuevo usuario</a>

<!-- TODO: Organizar estructura correcta -->
 <!-- CREAR USUARIO -->

<h2>Crear Usuario</h2>
<form method="post">
  Nombre: <input type="text" name="nombre" required><br>
  Apellido: <input type="text" name="apellido" required><br>
  Nickname: <input type="text" name="nickname" required><br>
  Email: <input type="email" name="email" required><br>
  Rol: 
  <select name="rol_id" required>
    <?php foreach($roles as $rol): ?>
      <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
    <?php endforeach; ?>
  </select><br>
  <button type="submit">Guardar</button>
</form>

<!-- MODIFICAR USUARIO -->
<h2>Editar Usuario</h2>
<form method="post">
  Nombre: <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required><br>
  Apellido: <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required><br>
  Nickname: <input type="text" name="nickname" value="<?= htmlspecialchars($usuario['nickname']) ?>" required><br>
  Email: <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required><br>
  Rol:
  <select name="rol_id" required>
    <?php foreach($roles as $rol): ?>
      <option value="<?= $rol['id_rol'] ?>" <?= $rol['id_rol']==$usuario['rol_id'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($rol['nombre']) ?>
      </option>
    <?php endforeach; ?>
  </select><br>
  <button type="submit">Actualizar</button>
</form>

<!-- ELIMINAR USUARIO -->
<a href="lista.php">Volver</a>