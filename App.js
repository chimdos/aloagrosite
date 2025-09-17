import { StatusBar } from 'expo-status-bar';
import { StyleSheet, Text, View, FlatList, SafeAreaView } from 'react-native';

const PIBBLES = [
  { id: '1', nome: 'Pibble' },
  { id: '2', nome: 'Gmail' },
  { id: '3', nome: 'Washington' },
  { id: '4', nome: 'Geeble' },
  { id: '5', nome: 'Bagel' },
  { id: '6', nome: 'Gus the Indifferent' },
  { id: '7', nome: 'Sir Charles Barkley' },
  { id: '8', nome: 'Franklin' },
  { id: '9', nome: 'Waffle' },
  { id: '10', nome: 'Jiggle' },
];

// Renomeei seu componente de item para seguir a convenção (começar com maiúscula)
// (ITEMGRID funcionaria, mas ItemGrid é o padrão)
const ItemGrid = ({ nome }) => (
  <View style={styles.itemcontainer}>
    <Text style={styles.itemnome}>{nome}</Text>
  </View>
);

export default function App() {
  return (
    // CORREÇÃO 1: 'safecontainer' mudou para 'safeContainer'
    <SafeAreaView style={styles.safeContainer}>

      {/* CORREÇÃO 2: 'Flatlist' mudou para 'FlatList' */}
      <FlatList
        data={PIBBLES}
        // Atualizei aqui para usar o nome do componente corrigido
        renderItem={({ item }) => <ItemGrid nome={item.nome} />}
        keyExtractor={item => item.id}
        numColumns={2}
        style={styles.grid}
      />

      <StatusBar style="auto" />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  // 'safeContainer' estava certo aqui, o erro era no uso (linha 41)
  safeContainer: {
    flex: 1,
    backgroundColor: '#fff',
    marginTop: StatusBar.currentHeight || 0,
  },
  grid: {
    flex: 1,
    padding: 8,
  },
  itemcontainer: {
    flex: 1,
    height: 150,
    margin: 8,
    backgroundColor: '#f0f0f0',
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: 10,
    borderWidth: 1,
    borderColor: '#ddd',
  },
  itemnome: {
    fontSize: 16,
    fontWeight: 'bold',
  },
});