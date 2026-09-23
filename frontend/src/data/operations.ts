export type TableStatus = 'livre' | 'ocupada' | 'reservada' | 'fechamento'
export type OrderStatus = 'pendente' | 'preparo' | 'pronto' | 'entregue'

export type Table = {
  id: number
  numero: number
  capacidade: number
  status: TableStatus
  cliente: string
  abertaEm?: string
}

export type Product = {
  id: number
  categoriaId: number
  categoria: string
  nome: string
  preco: number
  disponivel: boolean
  tempo: number
}

export type OrderItem = {
  id: number
  produtoId: number
  nome: string
  quantidade: number
  precoUnitario: number
  observacao?: string
}

export type Order = {
  id: number
  mesaId: number
  comandaId: number
  status: OrderStatus
  criadoEm: string
  funcionario: string
  observacao?: string
  itens: OrderItem[]
}

export const tables: Table[] = [
  { id: 1, numero: 1, capacidade: 2, status: 'livre', cliente: '-' },
  { id: 2, numero: 2, capacidade: 4, status: 'ocupada', cliente: 'Marina', abertaEm: '18:42' },
  { id: 3, numero: 3, capacidade: 4, status: 'ocupada', cliente: 'Rafael', abertaEm: '19:05' },
  { id: 4, numero: 4, capacidade: 6, status: 'reservada', cliente: 'Ana', abertaEm: '20:00' },
  { id: 5, numero: 5, capacidade: 2, status: 'fechamento', cliente: 'Joao', abertaEm: '18:18' },
  { id: 6, numero: 6, capacidade: 8, status: 'livre', cliente: '-' },
]

export const products: Product[] = [
  { id: 1, categoriaId: 1, categoria: 'Entradas', nome: 'Bruschetta', preco: 28, disponivel: true, tempo: 8 },
  { id: 2, categoriaId: 1, categoria: 'Entradas', nome: 'Croqueta de costela', preco: 34, disponivel: true, tempo: 12 },
  { id: 3, categoriaId: 2, categoria: 'Principais', nome: 'Risoto de cogumelos', preco: 62, disponivel: true, tempo: 22 },
  { id: 4, categoriaId: 2, categoria: 'Principais', nome: 'Mignon ao demi-glace', preco: 78, disponivel: true, tempo: 26 },
  { id: 5, categoriaId: 3, categoria: 'Sobremesas', nome: 'Cheesecake', preco: 29, disponivel: true, tempo: 10 },
  { id: 6, categoriaId: 4, categoria: 'Bebidas', nome: 'Soda de maracuja', preco: 18, disponivel: true, tempo: 4 },
  { id: 7, categoriaId: 4, categoria: 'Bebidas', nome: 'Agua com gas', preco: 9, disponivel: true, tempo: 1 },
]

export const orders: Order[] = [
  {
    id: 101,
    mesaId: 2,
    comandaId: 8801,
    status: 'preparo',
    criadoEm: '18:47',
    funcionario: 'Camila',
    observacao: 'Sem cebola na entrada',
    itens: [
      { id: 1, produtoId: 1, nome: 'Bruschetta', quantidade: 1, precoUnitario: 28 },
      { id: 2, produtoId: 4, nome: 'Mignon ao demi-glace', quantidade: 2, precoUnitario: 78 },
    ],
  },
  {
    id: 102,
    mesaId: 3,
    comandaId: 8802,
    status: 'pendente',
    criadoEm: '19:09',
    funcionario: 'Diego',
    itens: [
      { id: 3, produtoId: 2, nome: 'Croqueta de costela', quantidade: 2, precoUnitario: 34 },
      { id: 4, produtoId: 6, nome: 'Soda de maracuja', quantidade: 2, precoUnitario: 18 },
    ],
  },
  {
    id: 103,
    mesaId: 5,
    comandaId: 8798,
    status: 'pronto',
    criadoEm: '18:31',
    funcionario: 'Camila',
    itens: [
      { id: 5, produtoId: 3, nome: 'Risoto de cogumelos', quantidade: 1, precoUnitario: 62 },
      { id: 6, produtoId: 5, nome: 'Cheesecake', quantidade: 1, precoUnitario: 29 },
    ],
  },
]
