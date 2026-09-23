import { useMemo, useState } from 'react'
import {
  orders as initialOrders,
  products,
  tables,
  type Order,
  type OrderItem,
  type OrderStatus,
  type Product,
} from '../data/operations'

type DashboardView = 'geral' | 'pedidos' | 'mesas' | 'cardapio'

type HomeProps = {
  initialView?: DashboardView
}

const statusLabels: Record<OrderStatus, string> = {
  pendente: 'Pendente',
  preparo: 'Em preparo',
  pronto: 'Pronto',
  entregue: 'Entregue',
}

const nextStatus: Partial<Record<OrderStatus, OrderStatus>> = {
  pendente: 'preparo',
  preparo: 'pronto',
  pronto: 'entregue',
}

const currency = new Intl.NumberFormat('pt-BR', {
  currency: 'BRL',
  style: 'currency',
})

function orderTotal(order: Order) {
  return order.itens.reduce((total, item) => total + item.quantidade * item.precoUnitario, 0)
}

function itemTotal(items: OrderItem[]) {
  return items.reduce((total, item) => total + item.quantidade * item.precoUnitario, 0)
}

function groupProductsByCategory(items: Product[]) {
  return items.reduce<Record<string, Product[]>>((groups, item) => {
    groups[item.categoria] = [...(groups[item.categoria] ?? []), item]
    return groups
  }, {})
}

export function Home({ initialView = 'geral' }: HomeProps) {
  const [activeView, setActiveView] = useState<DashboardView>(initialView)
  const [selectedTableId, setSelectedTableId] = useState(2)
  const [orders, setOrders] = useState<Order[]>(initialOrders)
  const [draftItems, setDraftItems] = useState<OrderItem[]>([])
  const [observation, setObservation] = useState('')

  const selectedTable = tables.find((table) => table.id === selectedTableId) ?? tables[0]
  const tableOrders = orders.filter((order) => order.mesaId === selectedTable.id)
  const openOrders = orders.filter((order) => order.status !== 'entregue')
  const readyOrders = orders.filter((order) => order.status === 'pronto')
  const revenue = orders.reduce((total, order) => total + orderTotal(order), 0)
  const productsByCategory = useMemo(() => groupProductsByCategory(products), [])

  function addDraftItem(product: Product) {
    setDraftItems((currentItems) => {
      const existingItem = currentItems.find((item) => item.produtoId === product.id)

      if (existingItem) {
        return currentItems.map((item) =>
          item.produtoId === product.id ? { ...item, quantidade: item.quantidade + 1 } : item,
        )
      }

      return [
        ...currentItems,
        {
          id: Date.now(),
          nome: product.nome,
          precoUnitario: product.preco,
          produtoId: product.id,
          quantidade: 1,
        },
      ]
    })
  }

  function removeDraftItem(productId: number) {
    setDraftItems((currentItems) =>
      currentItems
        .map((item) =>
          item.produtoId === productId ? { ...item, quantidade: item.quantidade - 1 } : item,
        )
        .filter((item) => item.quantidade > 0),
    )
  }

  function submitOrder() {
    if (draftItems.length === 0) {
      return
    }

    const newOrder: Order = {
      comandaId: 8900 + orders.length,
      criadoEm: new Date().toLocaleTimeString('pt-BR', {
        hour: '2-digit',
        minute: '2-digit',
      }),
      funcionario: 'Atendente',
      id: 200 + orders.length,
      itens: draftItems,
      mesaId: selectedTable.id,
      observacao: observation || undefined,
      status: 'pendente',
    }

    setOrders((currentOrders) => [newOrder, ...currentOrders])
    setDraftItems([])
    setObservation('')
    setActiveView('pedidos')
  }

  function advanceOrder(orderId: number) {
    setOrders((currentOrders) =>
      currentOrders.map((order) => {
        const newStatus = nextStatus[order.status]
        return order.id === orderId && newStatus ? { ...order, status: newStatus } : order
      }),
    )
  }

  function closeTableOrders() {
    setOrders((currentOrders) =>
      currentOrders.map((order) =>
        order.mesaId === selectedTable.id ? { ...order, status: 'entregue' } : order,
      ),
    )
  }

  return (
    <main className="operations-page">
      <section className="page-toolbar">
        <div>
          <span className="eyebrow">Painel de gestao</span>
          <h1>Pedidos, mesas e comandas</h1>
          <p>Controle o atendimento do salao, envie pedidos para a cozinha e acompanhe o fechamento.</p>
        </div>

        <div className="view-tabs" role="tablist" aria-label="Modulos da operacao">
          {[
            ['geral', 'Geral'],
            ['pedidos', 'Pedidos'],
            ['mesas', 'Mesas'],
            ['cardapio', 'Cardapio'],
          ].map(([view, label]) => (
            <button
              aria-selected={activeView === view}
              className={activeView === view ? 'active' : ''}
              key={view}
              onClick={() => setActiveView(view as DashboardView)}
              role="tab"
              type="button"
            >
              {label}
            </button>
          ))}
        </div>
      </section>

      <section className="metrics-grid" aria-label="Resumo da operacao">
        <article>
          <span>Mesas ocupadas</span>
          <strong>{tables.filter((table) => table.status === 'ocupada').length}</strong>
          <small>{tables.length} mesas cadastradas</small>
        </article>
        <article>
          <span>Pedidos abertos</span>
          <strong>{openOrders.length}</strong>
          <small>{readyOrders.length} prontos para entregar</small>
        </article>
        <article>
          <span>Faturamento parcial</span>
          <strong>{currency.format(revenue)}</strong>
          <small>comandas do turno atual</small>
        </article>
        <article>
          <span>Tempo medio</span>
          <strong>18 min</strong>
          <small>da cozinha ao prato pronto</small>
        </article>
      </section>

      <section className="operations-grid">
        {(activeView === 'geral' || activeView === 'mesas') && (
          <aside className="panel tables-panel">
            <div className="panel-heading">
              <div>
                <span>Mesas</span>
                <h2>Salao</h2>
              </div>
              <button className="plain-button" type="button">
                Nova mesa
              </button>
            </div>

            <div className="table-grid">
              {tables.map((table) => (
                <button
                  className={`table-button ${table.status} ${
                    table.id === selectedTable.id ? 'selected' : ''
                  }`}
                  key={table.id}
                  onClick={() => setSelectedTableId(table.id)}
                  type="button"
                >
                  <strong>Mesa {table.numero}</strong>
                  <span>{table.capacidade} lugares</span>
                  <small>{table.status}</small>
                </button>
              ))}
            </div>
          </aside>
        )}

        {(activeView === 'geral' || activeView === 'cardapio') && (
          <section className="panel order-panel">
            <div className="panel-heading">
              <div>
                <span>Pedido ativo</span>
                <h2>Mesa {selectedTable.numero}</h2>
              </div>
              <strong className="table-customer">{selectedTable.cliente}</strong>
            </div>

            <div className="product-groups">
              {Object.entries(productsByCategory).map(([category, categoryProducts]) => (
                <div className="product-group" key={category}>
                  <h3>{category}</h3>
                  <div className="product-list">
                    {categoryProducts.map((product) => (
                      <button
                        disabled={!product.disponivel}
                        key={product.id}
                        onClick={() => addDraftItem(product)}
                        type="button"
                      >
                        <span>{product.nome}</span>
                        <small>
                          {currency.format(product.preco)} · {product.tempo} min
                        </small>
                      </button>
                    ))}
                  </div>
                </div>
              ))}
            </div>
          </section>
        )}

        {(activeView === 'geral' || activeView === 'cardapio') && (
          <aside className="panel draft-panel">
            <div className="panel-heading">
              <div>
                <span>Comanda</span>
                <h2>Novo pedido</h2>
              </div>
              <strong>{currency.format(itemTotal(draftItems))}</strong>
            </div>

            <div className="draft-list">
              {draftItems.length === 0 ? (
                <p className="empty-state">Selecione itens do cardapio para montar o pedido.</p>
              ) : (
                draftItems.map((item) => (
                  <div className="draft-item" key={item.produtoId}>
                    <div>
                      <strong>{item.nome}</strong>
                      <span>
                        {item.quantidade} x {currency.format(item.precoUnitario)}
                      </span>
                    </div>
                    <div className="quantity-actions">
                      <button onClick={() => removeDraftItem(item.produtoId)} type="button">
                        -
                      </button>
                      <button
                        onClick={() =>
                          addDraftItem({
                            categoria: '',
                            categoriaId: 0,
                            disponivel: true,
                            id: item.produtoId,
                            nome: item.nome,
                            preco: item.precoUnitario,
                            tempo: 0,
                          })
                        }
                        type="button"
                      >
                        +
                      </button>
                    </div>
                  </div>
                ))
              )}
            </div>

            <label className="field-label">
              Observacao do pedido
              <textarea
                onChange={(event) => setObservation(event.target.value)}
                placeholder="Ex: sem cebola, ponto da carne, alergias"
                rows={3}
                value={observation}
              />
            </label>

            <button className="primary-button full-width" disabled={draftItems.length === 0} onClick={submitOrder} type="button">
              Enviar pedido para cozinha
            </button>
          </aside>
        )}

        {(activeView === 'geral' || activeView === 'pedidos') && (
          <section className="panel queue-panel">
            <div className="panel-heading">
              <div>
                <span>Cozinha</span>
                <h2>Fila de pedidos</h2>
              </div>
              <small>{openOrders.length} em andamento</small>
            </div>

            <div className="order-columns">
              {(['pendente', 'preparo', 'pronto'] as OrderStatus[]).map((status) => (
                <div className="order-column" key={status}>
                  <h3>{statusLabels[status]}</h3>
                  {orders
                    .filter((order) => order.status === status)
                    .map((order) => (
                      <article className="order-card" key={order.id}>
                        <div className="order-card-heading">
                          <strong>Mesa {tables.find((table) => table.id === order.mesaId)?.numero}</strong>
                          <span>#{order.id}</span>
                        </div>
                        <p>
                          {order.itens.map((item) => `${item.quantidade}x ${item.nome}`).join(', ')}
                        </p>
                        {order.observacao ? <small>{order.observacao}</small> : null}
                        <div className="order-card-footer">
                          <span>{order.criadoEm}</span>
                          <button onClick={() => advanceOrder(order.id)} type="button">
                            {status === 'pronto' ? 'Entregar' : 'Avancar'}
                          </button>
                        </div>
                      </article>
                    ))}
                </div>
              ))}
            </div>
          </section>
        )}

        {(activeView === 'geral' || activeView === 'pedidos') && (
          <aside className="panel bill-panel">
            <div className="panel-heading">
              <div>
                <span>Fechamento</span>
                <h2>Mesa {selectedTable.numero}</h2>
              </div>
              <strong>{currency.format(tableOrders.reduce((total, order) => total + orderTotal(order), 0))}</strong>
            </div>

            <div className="bill-list">
              {tableOrders.length === 0 ? (
                <p className="empty-state">Nenhum pedido nessa mesa.</p>
              ) : (
                tableOrders.map((order) => (
                  <div className="bill-item" key={order.id}>
                    <span>Pedido #{order.id}</span>
                    <strong>{currency.format(orderTotal(order))}</strong>
                    <small>{statusLabels[order.status]}</small>
                  </div>
                ))
              )}
            </div>

            <button className="secondary-button full-width" onClick={closeTableOrders} type="button">
              Fechar comanda
            </button>
          </aside>
        )}
      </section>
    </main>
  )
}
