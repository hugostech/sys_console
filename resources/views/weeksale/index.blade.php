@extends('master')
@section('script')
    <script src="https://unpkg.com/react@16/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@16/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/babel-standalone@6.15.0/babel.min.js"></script>
@endsection
@section('mainContent')
    <div id="root"></div>
    <script type="text/babel">
        class product extends ReactDOM.component{

            render(){
                return <tr>{
                    this.props.data-detail.map((key,value)=><td>{value}</td>)
                }</tr>
            }
        }

        class sales extends ReactDOM.component {
            constructor(props){
                super(props);
                this.state = {
                    error: null,
                    isLoaded: false,
                    products: []
                }
            }

            componentDidMount(){
                fetch("{{url('')}}")
            }


            render(){
                return <table className="table table-bordered">{
                    this.state.products.map((key,data)=><product data-id={key} data-detail={data} />)
                }</table>;
            }
        }

        ReactDOM.render(
            <h1>Hello, world!</h1>,
            document.getElementById('root')
        );

    </script>
@endsection