let dientes = [
    { dientes: "muela", image: "muela.png" },
    { dientes: "diente", image: "diente.png" },
]

const listado = dientes.map((item) => {
    return item.image
}
)

console.log(listado)