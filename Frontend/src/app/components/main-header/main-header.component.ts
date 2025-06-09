import { Component } from "@angular/core";
import { RouteHandler } from "../../../handlers/route.handler";

@Component({
  selector: "app-main-header",
  imports: [],
  templateUrl: "./main-header.component.html",
  styleUrl: "./main-header.component.css",
})
export class MainHeaderComponent {
  constructor(protected readonly routeHandler: RouteHandler) {}
}
