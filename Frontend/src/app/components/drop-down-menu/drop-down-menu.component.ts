import { AfterViewInit, Component, ElementRef, Input, OnDestroy, ViewChild } from "@angular/core";
import { DropDownType } from "../../../enums/drop-down-type.enum";
import { fromEvent, Subscription } from "rxjs";

@Component({
  selector: "app-drop-down-menu",
  imports: [],
  templateUrl: "./drop-down-menu.component.html",
  styleUrl: "./drop-down-menu.component.css",
})
export class DropDownMenuComponent implements AfterViewInit, OnDestroy {
  @Input()
  public type: DropDownType = DropDownType.File;

  @ViewChild("menuElement")
  private menuElement?: ElementRef;

  private resizeEvent: Subscription;
  private initialRect?: DOMRect;

  constructor() {
    this.resizeEvent = fromEvent(window, "resize", () => {
      this.handleResize();
    }).subscribe();
  }

  ngAfterViewInit(): void {
    this.handleInitialPosition();
  }

  ngOnDestroy(): void {
    this.resizeEvent.unsubscribe();
  }

  private handleInitialPosition() {
    const element = this.menuElement?.nativeElement as HTMLDivElement;
    if (!element) {
      return;
    }

    this.initialRect = element.getBoundingClientRect();
    this.handleResize();
  }

  private handleResize() {
    const element = this.menuElement?.nativeElement as HTMLDivElement;
    if (!element) {
      return;
    }

    const initialRect = this.initialRect;
    if (!initialRect) {
      return;
    }

    const rect = element.getBoundingClientRect();
    if (rect.left < 0) {
      element.style.left = "1rem";
    }

    if (rect.right > window.innerWidth) {
      element.style.right = "1rem";
    }

    if (rect.bottom < 0) {
      element.style.bottom = "1rem";
    }

    if (rect.top > window.innerHeight) {
      element.style.top = "1rem";
    }

    if (rect.left > initialRect.left) {
      element.style.right = "auto";
    }
  }
}
