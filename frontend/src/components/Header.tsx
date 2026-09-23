import { NavLink } from 'react-router-dom'

type HeaderProps = {
  isLoggedIn: boolean
  setIsLoggedIn: (value: boolean) => void
}

const navItems = [
  { label: 'Operacao', to: '/' },
  { label: 'Pedidos', to: '/pedidos' },
  { label: 'Mesas', to: '/mesas' },
  { label: 'Cardapio', to: '/cardapio' },
]

export function Header({ isLoggedIn, setIsLoggedIn }: HeaderProps) {
  return (
    <header className="site-header">
      <NavLink className="brand" to="/" aria-label="Ir para o inicio">
        <span className="brand-mark">G</span>
        <span>
          <strong>Gestao Restaurante</strong>
          <small>Pedidos e comandas</small>
        </span>
      </NavLink>

      <nav className="main-nav" aria-label="Navegacao principal">
        {navItems.map((item) => (
          <NavLink key={item.to} to={item.to} end={item.to === '/'}>
            {item.label}
          </NavLink>
        ))}
      </nav>

      <button className="ghost-button" type="button" onClick={() => setIsLoggedIn(!isLoggedIn)}>
        {isLoggedIn ? 'Sessao ativa' : 'Entrar'}
      </button>
    </header>
  )
}
