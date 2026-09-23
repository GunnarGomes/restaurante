import { useState } from 'react'
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom'
import './App.css'
import { Footer } from './components/Footer'
import { Header } from './components/Header'
import { Home } from './pages/Home'

function App() {
  const [isLoggedIn, setIsLoggedIn] = useState(false)

  return (
    <Router>
      <Header isLoggedIn={isLoggedIn} setIsLoggedIn={setIsLoggedIn} />
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/pedidos" element={<Home initialView="pedidos" />} />
        <Route path="/mesas" element={<Home initialView="mesas" />} />
        <Route path="/cardapio" element={<Home initialView="cardapio" />} />
      </Routes>
      <Footer />
    </Router>
  )
}

export default App
