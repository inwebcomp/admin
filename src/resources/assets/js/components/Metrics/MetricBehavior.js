export default {
  created() {
    App.$on('metric-refresh', () => {
      this.fetch()
    })

    if (this.card.refreshWhenActionRuns) {
      App.$on('action-executed', () => this.fetch())
    }
  },

  destroyed() {
    App.$off('metric-refresh')
    App.$off('action-executed')
  },
}
